<?php

use Devdojo\Auth\Http\Controllers\VerifyEmailController;
use Devdojo\Auth\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

// Create redirect routes for common authentication routes

Route::redirect('login', 'auth/login')->name('login');
Route::redirect('register', 'auth/register')->name('register');

// define the logout route
Route::middleware(['auth', 'web'])->group(function () {
    Route::post('logout', LogoutController::class)
        ->name('logout');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});