<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GameApiService
{
    private string $baseUrl;
    private string $baseUrlAuth;
    private string $twitchId;
    private string $twitchSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.game_api.url');
        $this->baseUrlAuth = config('services.game_api.twitch');
        $this->twitchId = config('services.game_api.twitch_id');
        $this->twitchSecret = config('services.game_api.twitch_secret');
    }

    public function authentication()
    {
        return Http::asForm()->post($this->baseUrlAuth . 'oauth2/token', [
            'client_id' => $this->twitchId,
            'client_secret' => $this->twitchSecret,
            'grant_type' => 'client_credentials',
        ]);

    }

    public function getAccessToken()
    {
        if (Cache::has('igdb_access_token')) {
            return Cache::get('igdb_access_token');
        }

        $response = $this->authentication()->json();

        Cache::put('igdb_access_token', $response['access_token'], now()->addSeconds($response['expires_in']));

        return $response['access_token'];
    }

    public function getGames()
    {
        return Http::withHeaders([
            'Client-ID' => $this->twitchId,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->withBody('fields name; limit *;', 'text/plain')->post($this->baseUrl . '/games')->json();
    }

    public function getGame(string $id)
    {
        $query = filter_var($id, FILTER_VALIDATE_INT) !== false ? 'where id = ' . (int) $id . ';' : 'search "' . addslashes($id) . '"; limit 15;';
        return Http::withHeaders([
            'Client-ID' => $this->twitchId,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->withBody(
                $query . ' fields name,summary,storyline,platforms.name,genres.name,artworks.image_id,first_release_date;',
                'text/plain'
            )->post($this->baseUrl . '/games')->json();
    }
}