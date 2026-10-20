<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('sends a password reset link and updates the password with its token', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertRedirect()
        ->assertSessionHas('status');

    $resetNotification = null;
    Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use (&$resetNotification) {
        $resetNotification = $notification;
        return true;
    });

    $this->post(route('password.update'), [
        'token' => $resetNotification->token,
        'email' => $user->email,
        'password' => 'A-new-password-123',
        'password_confirmation' => 'A-new-password-123',
    ])->assertRedirect(route('login'));

    expect(Hash::check('A-new-password-123', $user->fresh()->password))->toBeTrue();
});

it('returns the same reset response for an unknown email', function () {
    $this->post(route('password.email'), ['email' => 'unknown@example.test'])
        ->assertRedirect()
        ->assertSessionHas('status', 'If an account uses that email, a password reset link will be sent.');
});

it('registers an unverified user, sends verification, and gates account features until verified', function () {
    Notification::fake();
    Queue::fake();

    $this->post('/register', [
        'name' => 'New User',
        'nickname' => 'new-user',
        'email' => 'new-user@example.test',
        'password' => 'A-valid-password-123',
        'password_confirmation' => 'A-valid-password-123',
        'bio' => '',
    ])->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'new-user@example.test')->firstOrFail();
    $this->assertAuthenticatedAs($user);
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmailNotification::class);

    $this->get('/users/'.$user->id.'/menu1/edit')->assertRedirect(route('verification.notice'));

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
        'id' => $user->id,
        'hash' => sha1($user->getEmailForVerification()),
    ]);
    $this->get($url)->assertRedirect('/');
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rate limits password reset link requests', function () {
    foreach (range(1, 5) as $_) {
        $this->post(route('password.email'), ['email' => 'missing@example.test'])->assertRedirect();
    }

    $this->post(route('password.email'), ['email' => 'missing@example.test'])->assertTooManyRequests();
});
