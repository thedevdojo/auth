<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

// Create redirect routes for common authentication routes

Route::redirect('login', 'auth/login')->name('login');
Route::redirect('register', 'auth/register')->name('register');

// define the logout route
Route::middleware(['auth', 'web'])->group(function () {
    Route::post('logout', LogoutController::class)
        ->name('logout');
});