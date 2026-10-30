<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('creates one verified demo account that can sign in with the documented credentials', function () {
    $this->seed(UserSeeder::class);
    $this->seed(UserSeeder::class);
    $user = User::where('email', 'test@example.com')->sole();

    expect(User::count())->toBe(1);
    expect($user->hasVerifiedEmail())->toBeTrue();

    $this->post('/login', ['email' => 'test@example.com', 'password' => 'password'])
        ->assertSessionHasNoErrors()->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('preserves an existing account and password when the demo seeder runs', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('existing-private-password'),
    ]);
    $before = $user->fresh()->getRawOriginal();

    $this->seed(UserSeeder::class);

    expect($user->fresh()->getRawOriginal())->toBe($before);
    expect(User::count())->toBe(1);
});

it('does not create a predictable demo account in production', function () {
    $this->app['env'] = 'production';

    $this->app->make(UserSeeder::class)->run();

    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});
