<?php

use App\Models\Review;
use App\Models\User;
use App\Services\GameApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
    });

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->get('/reviews/' . $review->id)
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
    });

    $user = User::factory()->create();
    $review = Review::factory()->for($user)->create();

    $this->actingAs($user)
        ->get('/reviews/' . $review->id)
        ->assertOk()
        ->assertSee($review->title);
});

it('returns 404 for a review that does not exist', function () {
    $this->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([]);
    });

    $this->get('/reviews/999999')->assertNotFound();
});