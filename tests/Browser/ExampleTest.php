<?php

use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('registers a user and sends them into the email verification flow', function () {
    Notification::fake();
    Queue::fake();
    $identifier = Str::lower(Str::random(12));
    $email = 'browser-'.$identifier.'@example.test';
    $password = 'A-valid-password-123';

    $this->get('/register')->assertOk();
    $this->post('/register', [
        'name' => 'Browser User',
        'nickname' => 'browser-'.$identifier,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
        'bio' => '',
    ])->assertRedirect(route('verification.notice'));

    $user = User::where('email', $email)->firstOrFail();
    $this->assertAuthenticatedAs($user);
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmailNotification::class);
    $this->get(route('verification.notice'))->assertOk()->assertSee('Verify your email address');
});
