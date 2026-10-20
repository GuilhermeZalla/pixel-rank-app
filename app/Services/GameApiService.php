<?php

namespace App\Services;

use App\Exceptions\GameApiUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GameApiService
{
    /**
     * Number of seconds to wait for the external API before giving up.
     */
    private const int TIMEOUT = 10;

    /**
     * Number of attempts made against the external API.
     */
    private const int RETRIES = 2;

    private string $baseUrl;
    private string $baseUrlAuth;
    private string $twitchId;
    private string $twitchSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.game_api.url'), '/');
        $this->baseUrlAuth = rtrim((string) config('services.game_api.twitch'), '/');
        $this->twitchId = (string) config('services.game_api.twitch_id');
        $this->twitchSecret = (string) config('services.game_api.twitch_secret');
    }

    /**
     * Determine whether the external game API has everything it needs to work.
     */
    public function isConfigured(): bool
    {
        return $this->baseUrl !== ''
            && $this->baseUrlAuth !== ''
            && $this->twitchId !== ''
            && $this->twitchSecret !== '';
    }

    /**
     * Run an upstream call, translating any failure into a
     * GameApiUnavailableException so callers can degrade gracefully.
     *
     * @param  callable(): Response  $callback
     * @return array<mixed>
     *
     * @throws GameApiUnavailableException
     */
    private function request(callable $callback): array
    {
        if (! $this->isConfigured()) {
            throw GameApiUnavailableException::notConfigured();
        }

        try {
            $response = $callback();
        } catch (GameApiUnavailableException $e) {
            throw $e;
        } catch (RequestException $e) {
            Log::warning('Game API responded with an error: ' . $e->getMessage());

            throw GameApiUnavailableException::upstreamFailure('The game API responded with an error.', $e);
        } catch (ConnectionException $e) {
            Log::warning('Game API connection failed: ' . $e->getMessage());

            throw GameApiUnavailableException::upstreamFailure('Could not reach the game API.', $e);
        } catch (Throwable $e) {
            Log::warning('Game API request failed: ' . $e->getMessage());

            throw GameApiUnavailableException::upstreamFailure('The game API request failed.', $e);
        }

        if ($response->failed()) {
            Log::warning('Game API responded with status ' . $response->status() . '.');

            throw GameApiUnavailableException::upstreamFailure('The game API responded with status ' . $response->status() . '.');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw GameApiUnavailableException::invalidResponse();
        }

        return $data;
    }

    /**
     * @return array<string, string>
     */
    private function requestHeaders(): array
    {
        return [
            'Client-ID' => $this->twitchId,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

    public function authentication()
    {
        return Http::asForm()
            ->timeout(self::TIMEOUT)
            ->retry(self::RETRIES, 200)
            ->post($this->baseUrlAuth . '/oauth2/token', [
                'client_id' => $this->twitchId,
                'client_secret' => $this->twitchSecret,
                'grant_type' => 'client_credentials',
            ]);
    }

    /**
     * @throws GameApiUnavailableException
     */
    public function getAccessToken(): string
    {
        if (Cache::has('igdb_access_token')) {
            $cached = Cache::get('igdb_access_token');

            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $response = $this->request(fn () => $this->authentication());

        $token = $response['access_token'] ?? null;
        $expiresIn = (int) ($response['expires_in'] ?? 0);

        if (! is_string($token) || $token === '') {
            throw GameApiUnavailableException::invalidResponse();
        }

        Cache::put('igdb_access_token', $token, now()->addSeconds($expiresIn > 0 ? $expiresIn : 3600));

        return $token;
    }

    /**
     * List of available platforms. Returns an empty list when the API is unavailable.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPlatforms(): array
    {
        try {
            return $this->request(fn () => Http::withHeaders($this->requestHeaders())
                ->timeout(self::TIMEOUT)
                ->retry(self::RETRIES, 200)
                ->withBody('fields id,name; limit 500; sort name asc;', 'text/plain')
                ->post($this->baseUrl . '/platforms'));
        } catch (GameApiUnavailableException $e) {
            return [];
        }
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array<int, array<string, mixed>>
     */
    public function getGamesCovers(array $ids): array
    {
        $ids = array_values(array_unique($ids));

        if ($ids === []) {
            return [];
        }

        $query = 'fields name,cover.image_id,artworks.image_id; where id = (' . implode(',', $ids) . ');';

        try {
            return Cache::remember(
                'igdb_games_' . implode('_', $ids),
                now()->addDay(),
                fn () => $this->request(fn () => Http::withHeaders($this->requestHeaders())
                    ->timeout(self::TIMEOUT)
                    ->retry(self::RETRIES, 200)
                    ->withBody($query, 'text/plain')
                    ->post($this->baseUrl . '/games'))
            );
        } catch (GameApiUnavailableException $e) {
            return [];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getGames(): array
    {
        try {
            return $this->request(fn () => Http::withHeaders($this->requestHeaders())
                ->timeout(self::TIMEOUT)
                ->retry(self::RETRIES, 200)
                ->withBody('fields name; limit *;', 'text/plain')
                ->post($this->baseUrl . '/games'));
        } catch (GameApiUnavailableException $e) {
            return [];
        }
    }

    /**
     * Fetch a single game. Returns an empty list when the game is unknown
     * or the API is unavailable.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getGame(string $id): array
    {
        $query = filter_var($id, FILTER_VALIDATE_INT) !== false
            ? 'where id = ' . (int) $id . ';'
            : 'search "' . addslashes($id) . '"; limit 15;';

        try {
            return $this->request(fn () => Http::withHeaders($this->requestHeaders())
                ->timeout(self::TIMEOUT)
                ->retry(self::RETRIES, 200)
                ->withBody(
                    $query . ' fields name,summary,storyline,platforms.name,genres.name,cover.image_id,artworks.image_id,first_release_date;',
                    'text/plain'
                )
                ->post($this->baseUrl . '/games'));
        } catch (GameApiUnavailableException $e) {
            return [];
        }
    }
}
