<?php

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows only the review owner to edit and update a review', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $review = Review::factory()->for($owner)->create();

    $this->get('/reviews/'.$review->id.'/edit')->assertRedirect('/login');
    $this->actingAs($other)->get('/reviews/'.$review->id.'/edit')->assertForbidden();
    $this->actingAs($owner)->get('/reviews/'.$review->id.'/edit')->assertOk();

    $payload = [
        'game_id' => $review->game_id,
        'game_name' => $review->game_name,
        'game_cover' => $review->game_cover,
        'title' => 'Updated title',
        'recommendation' => 'recommended',
        'contains_spoilers' => '0',
        'body' => str_repeat('Review text. ', 20),
        'rating' => '8.5',
        'platform_playable' => 'PC',
    ];

    $this->actingAs($other)->put('/reviews/'.$review->id, $payload)->assertForbidden();
    $this->actingAs($owner)->put('/reviews/'.$review->id, $payload)->assertRedirect('/reviews/'.$review->id);
    expect($review->fresh()->title)->toBe('Updated title');
});

it('allows only a comment owner to edit a comment and validates comment review references', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $review = Review::factory()->for($owner)->create();
    $comment = $owner->comments()->create([
        'body' => 'Original comment text',
        'review_id' => $review->id,
    ]);

    $this->actingAs($other)->patchJson('/comments/'.$comment->id, ['comment' => 'Updated comment body'])->assertForbidden();
    $this->actingAs($owner)->patchJson('/comments/'.$comment->id, ['comment' => 'Updated comment body'])->assertOk();
    expect($comment->fresh()->body)->toBe('Updated comment body');

    $this->actingAs($owner)->post('/comments', [
        'comment' => 'A valid length comment',
        'review' => 999999,
    ])->assertRedirect()->assertSessionHasErrors('review');
});

it('shows public profile fields to authenticated users and keeps the user directory closed', function () {
    $profile = User::factory()->create(['bio' => 'Public bio']);

    $this->get('/users/'.$profile->id)->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get('/users/'.$profile->id)
        ->assertOk()
        ->assertSee($profile->nickname)
        ->assertSee('Public bio')
        ->assertDontSee($profile->email);

    $this->get('/users')->assertForbidden();
});

it('redirects the comments page to the authenticated user dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/comments')->assertRedirect('/users/'.$user->id.'/menu3/edit');
});
