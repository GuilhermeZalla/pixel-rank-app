<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Auth\LogoutController;
use \App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GameController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;

// Search Routes

Route::post('/locale', LocaleController::class)->middleware('throttle:10,1')->name('locale.update');
Route::get('/search/{filter?}/{search?}', [SearchController::class, 'index']);
Route::get('/games/search', GameController::class)->middleware('throttle:30,1');

// Auth Route List

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'create')->name('login');
    Route::get('/register', 'create');
    Route::post('/login', 'store')->middleware('throttle:5,1');
});

Route::delete('/logout', LogoutController::class)->middleware('auth');

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'create');
    Route::post('/register', 'store')->middleware('throttle:5,1');
});

Route::controller(PasswordController::class)->group(function () {
    Route::get('/forgot-password', 'request')->middleware('guest')->name('password.request');
    Route::post('/forgot-password', 'email')->middleware(['guest', 'throttle:5,1'])->name('password.email');
    Route::get('/reset-password/{token}', 'resetForm')->middleware('guest')->name('password.reset');
    Route::post('/reset-password', 'reset')->middleware(['guest', 'throttle:5,1'])->name('password.update');
});

Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect('/')->with('success', __('auth_ui.email_already_verified'));
    }

    $request->fulfill();

    return redirect('/')->with('success', __('auth_ui.email_verified_success'));
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');
Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Notifications Route

Route::controller(NotificationController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', 'index')->name('notifications.index');
    Route::patch('/notifications/{id}/read', 'markAsRead')->name('notifications.read');
    Route::patch('/notifications/read-all', 'markAllAsRead')->name('notifications.read-all');
    Route::patch('/users/theme', 'updateTheme')->name('users.theme');
});

// Users Route

Route::controller(UserController::class)->middleware(['auth', 'verified'])->group(function () {
    Route::get('/users', 'index')->can('viewAny', \App\Models\User::class);
    Route::get('/users/{user}', 'show')->can('view', 'user');
    Route::get('/users/{user}/{menu?}/edit', 'edit')->name('users.edit')->can('update', 'user');
    Route::put('/users/{user}', 'update')->can('update', 'user');
    Route::get('/users/{user}/export', 'exportData')->name('users.export')->can('update', 'user');
    Route::delete('/users/{user}', 'destroy')->can('delete', 'user');
});

// Reviews Route

Route::controller(ReviewController::class)->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::post('/reviews/create', 'create');
        Route::get('/reviews/create', 'create')->name('reviews.create');
        Route::post('/reviews', 'store')->can('create', \App\Models\Review::class);
        Route::put('/reviews/{review}', 'update')->can('update', 'review');
        Route::delete('/reviews/{review}', 'destroy')->can('delete', 'review');
        Route::get('/reviews/{review}/edit', 'edit')->can('update', 'review');
    });
    Route::get('/reviews/{review}', 'show');
    Route::get('/{filter?}', 'index')->where('filter', 'essential|recommended|not_recommended|mixed|highest-rated|lowest-rated|popular|oldest|hot-reviews')->name('reviews');
});

// Comments Route

Route::controller(CommentController::class)->middleware(['auth', 'verified'])->group(function(){
    Route::get('/comments', 'index');
    Route::post('/comments', 'store');
    Route::patch('/comments/{comment}', 'update')->can('update', 'comment');
    Route::delete('/comments/{comment}', 'destroy')->can('delete', 'comment');
});

Route::fallback(function () {
    if (request()->is('api/*') || request()->expectsJson()) {
        abort(404);
    }

    return response()->view('errors.404', ['status' => 404], 404);
});
