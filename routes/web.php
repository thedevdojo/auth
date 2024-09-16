<?php

use Devdojo\Auth\Http\Controllers\LogoutController;
use Devdojo\Auth\Http\Controllers\SocialController;
use Devdojo\Auth\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Create redirect routes for common authentication routes

Route::redirect('login', 'auth/login')->name('login');
Route::redirect('register', 'auth/register')->name('register');

// define the logout route
Route::middleware(['auth', 'web'])->group(function () {

    Route::post('/auth/logout', LogoutController::class)
        ->name('logout');

    Route::get('/auth/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/auth/logout', [LogoutController::class, 'getLogout'])->name('logout.get');

});

Route::middleware(['web'])->group(function () {
    // Add social redirect and callback routes
    Route::get('auth/{driver}/redirect', [SocialController::class, 'redirect']);
    Route::get('auth/{driver}/callback', [SocialController::class, 'callback']);

});
