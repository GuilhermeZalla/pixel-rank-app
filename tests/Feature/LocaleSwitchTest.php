<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

it('provides matching translation keys in both locales', function () {
    $englishFiles = collect(File::files(lang_path('en')))->map(fn ($file) => $file->getFilename())->sort()->values();
    $portugueseFiles = collect(File::files(lang_path('pt_BR')))->map(fn ($file) => $file->getFilename())->sort()->values();

    expect($portugueseFiles->all())->toBe($englishFiles->all());

    foreach ($englishFiles as $file) {
        $englishKeys = array_keys(Arr::dot(require lang_path('en/'.$file)));
        $portugueseKeys = array_keys(Arr::dot(require lang_path('pt_BR/'.$file)));
        sort($englishKeys);
        sort($portugueseKeys);

        expect($portugueseKeys)->toBe($englishKeys);
    }
});

it('detects Portuguese from the browser language on the first request', function () {
    $this->get('/login', ['Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8'])
        ->assertOk()
        ->assertSee('<html lang="pt-BR"', false);

    expect(session('locale'))->toBe('pt_BR');
});

it('persists a manually selected locale in the session and cookie', function () {
    $this->get('/login')->assertOk();

    $this->post(route('locale.update'), ['locale' => 'pt_BR'])
        ->assertRedirect()
        ->assertCookie('locale', 'pt_BR');

    expect(session('locale'))->toBe('pt_BR');

    $this->get('/login', ['Accept-Language' => 'en-US,en;q=0.9'])
        ->assertOk()
        ->assertSee('<html lang="pt-BR"', false);
});

it('rejects a locale outside the supported list', function () {
    $this->post(route('locale.update'), ['locale' => 'fr'])
        ->assertRedirect()
        ->assertSessionHasErrors('locale');
});

it('uses the active locale for validation messages', function () {
    app()->setLocale('pt_BR');
    $validator = Validator::make([], ['email' => ['required']]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('email'))->toBe("O campo e-mail \u{00E9} obrigat\u{00F3}rio.");
});

it('renders custom authentication notifications in the active locale', function () {
    $user = new \App\Models\User();
    $user->id = 42;
    $user->email = 'locale@example.test';

    app()->setLocale('pt_BR');
    $verification = (new \App\Notifications\VerifyEmailNotification())->toMail($user);
    $reset = (new \App\Notifications\ResetPasswordNotification('reset-token'))->toMail($user);

    expect($verification->subject)->toBe("Confirme seu endere\u{00E7}o de e-mail")
        ->and($reset->subject)->toBe('Redefina sua senha');

    app()->setLocale('en');
    $verification = (new \App\Notifications\VerifyEmailNotification())->toMail($user);
    $reset = (new \App\Notifications\ResetPasswordNotification('reset-token'))->toMail($user);

    expect($verification->subject)->toBe('Verify your email address')
        ->and($reset->subject)->toBe('Reset your password');
});

it('renders localized not found pages in both supported locales', function () {
    $this->get('/missing-page-for-locale-check', ['Accept-Language' => 'pt-BR'])
        ->assertNotFound()
        ->assertSee('<html lang="pt-BR"', false)
        ->assertSee("P\u{00E1}gina n\u{00E3}o encontrada");

    $this->withSession(['locale' => 'en'])
        ->get('/another-missing-page-for-locale-check', ['Accept-Language' => 'en-US'])
        ->assertNotFound()
        ->assertSee('<html lang="en-US"', false)
        ->assertSee('Page not found');
});

it('renders the login screen in English when selected by the browser', function () {
    $this->get('/login', ['Accept-Language' => 'en-US,en;q=0.9'])
        ->assertOk()
        ->assertSee('<html lang="en-US"', false);
});
