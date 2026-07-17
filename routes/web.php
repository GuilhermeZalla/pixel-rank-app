<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Auth\LoginController;
use \App\Http\Controllers\Auth\LogoutController;
use \App\Http\Controllers\Auth\RegisterController;
use \App\Http\Controllers\HomeController;


// Home Route

Route::get('/', [HomeController::class, 'index']);

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
