<?php

use App\Models\Review;
use App\Models\User;
use App\Services\GameApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function withoutGameApiConfiguration(): void
{
    config([
        'services.game_api.twitch' => '',
        'services.game_api.twitch_id' => '',
        'services.game_api.twitch_secret' => '',
        'services.game_api.url' => '',
        'services.game_api.key' => '',
    ]);
}

function withGameApiConfiguration(): void
{
    config([
        'services.game_api.twitch' => 'https://id.twitch.tv',
        'services.game_api.twitch_id' => 'client-id',
        'services.game_api.twitch_secret' => 'client-secret',
        'services.game_api.url' => 'https://api.igdb.com/v4',
        'services.game_api.key' => 'api-key',
    ]);
}

it('reports the game api as unconfigured when the credentials are missing', function () {
    withoutGameApiConfiguration();

    expect(app(GameApiService::class)->isConfigured())->toBeFalse();
});

it('returns empty collections when the game api is not configured', function () {
    withoutGameApiConfiguration();

    $service = app(GameApiService::class);

    expect($service->getPlatforms())->toBe([])
        ->and($service->getGames())->toBe([])
        ->and($service->getGame('zelda'))->toBe([])
        ->and($service->getGamesCovers([1, 2]))->toBe([]);
});

it('never calls the upstream api when it is not configured', function () {
    withoutGameApiConfiguration();

    Http::fake();

    app(GameApiService::class)->getPlatforms();
    app(GameApiService::class)->getGame('zelda');

    Http::assertNothingSent();
});

it('returns empty collections when the upstream api fails', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response(['access_token' => 'token', 'expires_in' => 3600], 200),
        'api.igdb.com/*' => Http::response(['error' => 'upstream failure'], 500),
    ]);

    $service = app(GameApiService::class);

    expect($service->getPlatforms())->toBe([])
        ->and($service->getGame('zelda'))->toBe([]);
});

it('returns empty collections when the upstream api cannot be reached', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response([], 500),
    ]);

    expect(app(GameApiService::class)->getPlatforms())->toBe([]);
});

it('returns the games and platforms when the upstream api answers', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response(['access_token' => 'token', 'expires_in' => 3600], 200),
        'api.igdb.com/v4/platforms' => Http::response([
            ['id' => 6, 'name' => 'PC'],
        ], 200),
        'api.igdb.com/v4/games' => Http::response([
            [
                'id' => 1020,
                'name' => 'Grand Theft Auto V',
                'summary' => 'A crime story.',
                'platforms' => [['name' => 'PC']],
                'first_release_date' => 1379635200,
            ],
        ], 200),
    ]);

    $service = app(GameApiService::class);

    expect($service->isConfigured())->toBeTrue()
        ->and($service->getPlatforms())->toHaveCount(1)
        ->and($service->getGame('GTA'))->toHaveCount(1);
});

it('renders the review page when the game api is not configured', function () {
    withoutGameApiConfiguration();

    Http::fake();

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee($review->title)
        ->assertSee('Not available');
});

it('renders the review page when the upstream api fails', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response([], 500),
    ]);

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee($review->title);
});

it('renders the review page with the game metadata when the api answers', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response(['access_token' => 'token', 'expires_in' => 3600], 200),
        'api.igdb.com/v4/games' => Http::response([
            [
                'id' => 1020,
                'name' => 'Grand Theft Auto V',
                'summary' => 'A crime story.',
                'platforms' => [['name' => 'PC'], ['name' => 'PlayStation 5']],
                'first_release_date' => 1379635200,
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create(['game_id' => 1020]);

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('A crime story.')
        ->assertSee('PC');
});

it('renders the review creation form when the game api is not configured', function () {
    withoutGameApiConfiguration();

    Http::fake();

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('reviews.create'))
        ->assertOk()
        ->assertSee('The game database is currently unavailable');
});

it('returns an empty json payload for the game search when the api is not configured', function () {
    withoutGameApiConfiguration();

    Http::fake();

    $this->getJson('/games/search?query=zelda')
        ->assertOk()
        ->assertExactJson([]);
});

it('returns an empty json payload for the game search when the upstream api fails', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response([], 500),
    ]);

    $this->getJson('/games/search?query=zelda')
        ->assertOk()
        ->assertExactJson([]);
});

it('returns the matching games for the game search when the api answers', function () {
    withGameApiConfiguration();

    Http::fake([
        'id.twitch.tv/*' => Http::response(['access_token' => 'token', 'expires_in' => 3600], 200),
        'api.igdb.com/v4/games' => Http::response([
            [
                'id' => 1020,
                'name' => 'Grand Theft Auto V',
                'cover' => ['image_id' => 'cover-id'],
            ],
        ], 200),
    ]);

    $this->getJson('/games/search?query=GTA')
        ->assertOk()
        ->assertJsonPath('0.name', 'Grand Theft Auto V')
        ->assertJsonPath('0.cover.image_id', 'cover-id');
});

it('returns an empty json payload for a blank game search query', function () {
    withoutGameApiConfiguration();

    $this->getJson('/games/search?query=')
        ->assertOk()
        ->assertExactJson([]);
});
