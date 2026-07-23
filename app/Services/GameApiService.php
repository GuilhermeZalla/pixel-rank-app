<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameApiService
{
    private string $baseUrl;
    private string $baseUrlAuth;
    private string $twitchId;
    private string $twitchSecret;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.game_api.url');
        $this->baseUrlAuth = config('services.game_api.twitch');
        $this->twitchId = config('services.game_api.twitch_id');
        $this->twitchSecret = config('services.game_api.twitch_secret');
    }

    public function authentication()
    {
        $response = Http::asForm()->post($this->baseUrlAuth . 'oauth2/token', [
            'client_id' => $this->twitchId,
            'client_secret' => $this->twitchSecret,
            'grant_type' => 'client_credentials',
        ]);

        $this->token = $response->json()['access_token'];
    }

    public function getGames()
    {
        return Http::withHeaders([
            'Client-ID' => $this->twitchId,
            'Authorization' => 'Bearer ' . $this->token,
        ])->withBody('fields *;', 'text/plain')->post($this->baseUrl)->json();
    }

    public function getGame(string $title)
    {
        return Http::withHeaders([
            'Client-ID' => $this->twitchId,
            'Authorization' => 'Bearer ' . $this->token,
        ])->withBody('search "' . $title . '"; fields name,cover.url,first_release_date; limit 10;',
        'text/plain')->post($this->baseUrl)->json();
    }
}