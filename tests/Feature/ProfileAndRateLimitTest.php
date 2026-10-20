<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('redirects a visitor who tries to update a profile', function () {
    $user = User::factory()->create();

    $this->put('/users/'.$user->id, ['name' => 'Changed name'])
        ->assertRedirect(route('login'));
});

it('prevents one user from updating another user profile', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($owner)
        ->put('/users/'.$otherUser->id, ['name' => 'Changed name'])
        ->assertForbidden();

    expect($otherUser->refresh()->name)->not->toBe('Changed name');
});

it('allows a user to update their own profile without changing their password', function () {
    $user = User::factory()->create([
        'name' => 'Original name',
        'nickname' => 'original-name',
        'email' => 'original@example.test',
    ]);

    $this->actingAs($user)
        ->put('/users/'.$user->id, [
            'name' => 'Updated name',
            'nickname' => 'original-name',
            'email' => 'original@example.test',
            'bio' => 'A short profile bio.',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect('/');

    expect($user->refresh()->name)->toBe('Updated name')
        ->and($user->bio)->toBe('A short profile bio.')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

it('rate limits repeated invalid login attempts', function () {
    foreach (range(1, 5) as $_) {
        $this->post('/login')->assertSessionHasErrors(['email', 'password']);
    }

    $this->post('/login')->assertTooManyRequests();
});

it('rate limits repeated invalid registration attempts', function () {
    foreach (range(1, 5) as $_) {
        $this->post('/register')->assertSessionHasErrors(['name', 'nickname', 'email', 'password']);
    }

    $this->post('/register')->assertTooManyRequests();
});

it('limits repeated game catalog searches', function () {
    $this->mock(\App\Services\GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->times(30)->andReturn([]);
    });

    foreach (range(1, 30) as $_) {
        $this->getJson('/games/search?query=123')->assertOk();
    }

    $this->getJson('/games/search?query=123')->assertTooManyRequests();
});
