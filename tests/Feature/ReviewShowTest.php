<?php

use App\Models\Review;
use App\Models\User;
use App\Services\GameApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function fakeGameApi(): void
{
    test()->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([
            [
                'platforms' => [],
                'first_release_date' => null,
                'summary' => 'Test summary',
                'cover' => null,
            ],
        ]);
        $mock->shouldReceive('getPlatforms')->andReturn([]);
        $mock->shouldReceive('isConfigured')->andReturn(true);
    });
}

it('renders the review page with the comment list container', function () {
    fakeGameApi();

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('id="comments"', false);
});

it('renders a review page for a review without comments', function () {
    fakeGameApi();

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    expect($review->comments)->toHaveCount(0);

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('id="comments"', false);
});

it('does not mark the spoiler checkbox as checked by default', function () {
    fakeGameApi();

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('reviews.create'))
        ->assertOk()
        ->assertSee('type="checkbox"', false)
        ->assertDontSee('checked="checked"', false);
});

it('keeps the typed value in the textarea after a validation error', function () {
    fakeGameApi();

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->from(route('reviews.create'))
        ->post('/reviews', [
            'title' => 'Test title',
            'body' => str_repeat('a', 200),
            'recommendation' => 'recommended',
            'rating' => 8,
            'platform_playable' => 'PC',
        ])
        ->assertSessionHasErrors(['game_id']);

    $this->actingAs($user)
        ->get(route('reviews.create'))
        ->assertOk()
        ->assertSee(str_repeat('a', 200), false);
});

it('shows the avatar through the public storage url', function () {
    fakeGameApi();

    $user = User::factory()->create(['avatar' => 'avatars/test.png']);
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('/storage/avatars/test.png', false);
});

it('applies the recommendation badge colour in Portuguese', function () {
    fakeGameApi();

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create(['recommendation' => 'recommended']);

    $response = $this->withSession(['locale' => 'pt_BR'])
        ->get('/reviews/'.$review->id);

    $response->assertOk()
        ->assertSee('bg-[#033317]', false);
});

it('shows a review to a visitor', function () {
    $this->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([
            [
                'platforms' => [],
                'first_release_date' => null,
                'summary' => 'Test summary',
                'cover' => null,
            ],
        ]);
        $mock->shouldReceive('isConfigured')->andReturn(true);
    });

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee($review->title);
});

it('shows a review to an authenticated user', function () {
    $this->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([
            [
                'platforms' => [],
                'first_release_date' => null,
                'summary' => 'Test summary',
                'cover' => null,
            ],
        ]);
        $mock->shouldReceive('isConfigured')->andReturn(true);
    });

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->actingAs($user)
        ->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee($review->title);
});

it('returns 404 for a review that does not exist', function () {
    $this->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([]);
    });

    $this->get('/reviews/999999')->assertNotFound();
});

it('uses the saved game cover on both the feed and a review published through the form', function () {
    fakeGameApi();
    $user = User::factory()->create();

    $this->actingAs($user)->post('/reviews', [
        'game_id' => 119133,
        'game_name' => 'Elden Ring',
        'game_cover' => 'co4jni',
        'title' => 'A world worth exploring',
        'body' => str_repeat('Exploring this game is rewarding. ', 8),
        'recommendation' => 'essential',
        'rating' => 9.5,
        'platform_playable' => 'PC',
    ])->assertSessionHasNoErrors()->assertRedirect();

    $review = $user->reviews()->sole();
    $cover = 'src="https://images.igdb.com/igdb/image/upload/t_1080p/co4jni.jpg"';

    $this->get('/')->assertOk()->assertSee($cover, false);
    $this->get('/reviews/'.$review->id)->assertOk()->assertSee($cover, false);
});

it('shows the saved game cover even when the game metadata api is unavailable', function () {
    $this->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([]);
        $mock->shouldReceive('isConfigured')->andReturn(false);
    });
    $review = Review::factory()->for(User::factory())->create(['game_cover' => 'co4jni']);

    $this->get('/reviews/'.$review->id)->assertOk()
        ->assertSee('src="https://images.igdb.com/igdb/image/upload/t_1080p/co4jni.jpg"', false);
});

it('shows the local placeholder when a review has no saved game cover', function () {
    fakeGameApi();
    $review = Review::factory()->for(User::factory())->create(['game_cover' => null]);

    $this->get('/reviews/'.$review->id)->assertOk()
        ->assertSee('src="'.asset('images/game-placeholder.svg').'"', false);
});

it('renders the Portuguese comment controls with valid accents', function () {
    fakeGameApi();
    $review = Review::factory()->for(User::factory())->create();

    $this->withSession(['locale' => 'pt_BR'])->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('Ordenar comentários')
        ->assertSee('Capa do jogo')
        ->assertSee('data-edit-save="Salvar alterações"', false)
        ->assertDontSee('Ã')
        ->assertDontSee('â€');
});
