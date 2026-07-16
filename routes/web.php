<?php

use Devdojo\Auth\Http\Controllers\LogoutController;
use Devdojo\Auth\Http\Controllers\SocialController;
use Devdojo\Auth\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Create redirect routes for common authentication routes

Route::redirect('login', 'auth/login')->name('login');
Route::redirect('register', 'auth/register')->name('register');

// Explicit routes for Livewire 4 pages
Route::livewire('/auth/login', 'auth.login');
Route::livewire('/auth/password/confirm', 'auth.password.confirm');
Route::livewire('/auth/password/reset', 'auth.password.reset');
Route::livewire('/auth/password/{token}', 'auth.password.[token]');
Route::livewire('/auth/register', 'auth.register');
Route::livewire('/auth/setup', 'auth.setup');
Route::livewire('/auth/setup/appearance', 'auth.setup.appearance');
Route::livewire('/auth/setup/language', 'auth.setup.language');
Route::livewire('/auth/setup/providers', 'auth.setup.providers');
Route::livewire('/auth/setup/settings', 'auth.setup.settings');
Route::livewire('/auth/two-factor-challenge', 'auth.two-factor-challenge');
Route::livewire('/auth/verify', 'auth.verify');
Route::livewire('/user/two-factor-authentication', 'user.two-factor-authentication');

// define the logout route
// 'web' must precede 'auth': in apps whose Authenticate middleware doesn't
// extend the framework's, middleware-priority sorting can't reorder the pair,
// so the literal order decides whether the session exists when auth runs.
Route::middleware(['web', 'auth'])->group(function () {

    Route::post('/auth/logout', LogoutController::class)
        ->name('logout');

    Route::get('/auth/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/auth/logout', LogoutController::class)->name('logout.get');

});

Route::middleware(['web'])->group(function () {
    // Add social redirect and callback routes
    Route::get('auth/{driver}/redirect', [SocialController::class, 'redirect']);
    Route::get('auth/{driver}/callback', [SocialController::class, 'callback']);

});
