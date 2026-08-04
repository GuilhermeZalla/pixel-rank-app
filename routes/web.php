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
use App\Services\GameApiService;

// Auth Route List

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'create')->name('login');
    Route::get('/register', 'create');
    Route::post('/login', 'store');
});

Route::delete('/logout', LogoutController::class);

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'create');
    Route::post('/register', 'store');
});

// Users Route

Route::controller(UserController::class)->middleware('auth')->group(function () {
    Route::get('/users', 'index');
    Route::get('/users/{user}', 'show');
    Route::get('/users/{user}/edit', 'edit')->name('users.edit')->can('update', 'user');
    Route::put('/users/{user}', 'update')->can('update', 'user');
});

// Reviews Route

Route::controller(ReviewController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::post('/reviews/create', 'create');
        Route::get('/reviews/create', 'create');
        Route::post('/reviews', 'store')->can('create', \App\Models\Review::class);
        Route::put('/reviews/{review}', 'update')->can('update', 'review');
        Route::delete('/reviews/{review}', 'destroy')->can('delete', 'review');
        Route::get('/reviews/{review}/edit', 'edit')->can('update', 'review');
    });
    Route::get('/reviews/{review}', 'show');
    Route::get('/{filter?}', 'index');
});

// Comments Route

Route::controller(CommentController::class)->middleware('auth')->group(function(){
    Route::get('/comments', 'index');
    Route::post('/comments', 'store');
    Route::patch('/comments/{comment}', 'update')->can('update', 'comment');
    Route::delete('/comments/{comment}', 'destroy')->can('delete', 'comment');
});

// Search Game Routes

Route::get('/games/search', GameController::class);