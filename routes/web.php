<?php

use Devdojo\Auth\Http\Controllers\LogoutController;
use Devdojo\Auth\Http\Controllers\SocialController;
use Devdojo\Auth\Http\Controllers\VerifyEmailController;
use Devdojo\Auth\Http\Middleware\GuestUnlessPreview;
use Devdojo\Auth\Http\Middleware\PreviewOr2FAThrottle;
use Devdojo\Auth\Http\Middleware\PreviewOrAuth;
use Devdojo\Auth\Http\Middleware\PreviewOrGuest;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::redirect('login', 'auth/login')->name('login');
    Route::redirect('register', 'auth/register')->name('register');

    Route::livewire('/auth/login', 'auth.login')
        ->middleware(GuestUnlessPreview::class)
        ->name('auth.login');

    Route::livewire('/auth/password/confirm', 'auth.password.confirm')
        ->middleware(PreviewOrAuth::class)
        ->name('password.confirm');

    Route::livewire('/auth/password/reset', 'auth.password.reset')->name('auth.password.request');
    Route::livewire('/auth/password/{token}', 'auth.password.token')->name('password.reset');

    Route::livewire('/auth/register', 'auth.register')
        ->middleware(PreviewOrGuest::class)
        ->name('auth.register');

    Route::middleware('view-auth-setup')->group(function () {
        Route::livewire('/auth/setup', 'auth.setup')->name('auth.setup');
        Route::livewire('/auth/setup/appearance', 'auth.setup.appearance')->name('auth.setup.appearance');
        Route::livewire('/auth/setup/language', 'auth.setup.language')->name('auth.setup.language');
        Route::livewire('/auth/setup/passkeys', 'auth.setup.passkeys')->name('auth.setup.passkeys');
        Route::livewire('/auth/setup/providers', 'auth.setup.providers')->name('auth.setup.providers');
        Route::livewire('/auth/setup/settings', 'auth.setup.settings')->name('auth.setup.settings');
    });

    Route::livewire('/auth/two-factor-challenge', 'auth.two-factor-challenge')
        ->middleware(PreviewOr2FAThrottle::class)
        ->name('auth.two-factor-challenge');

    Route::livewire('/auth/verify', 'auth.verify')
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.notice');

    Route::middleware(['auth', 'verified', 'two-factor-enabled'])->group(function () {
        Route::livewire('/user/two-factor-authentication', 'user.two-factor-authentication')
            ->name('user.two-factor-authentication');
    });

    // 'web' must precede 'auth': in apps whose Authenticate middleware doesn't
    // extend the framework's, middleware-priority sorting can't reorder the pair,
    // so the literal order decides whether the session exists when auth runs.
    Route::middleware(['auth'])->group(function () {
        Route::post('/auth/logout', LogoutController::class)
            ->name('logout');

        Route::get('/auth/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::get('/auth/logout', LogoutController::class)->name('logout.get');
    });

    Route::get('auth/{driver}/redirect', [SocialController::class, 'redirect']);
    Route::get('auth/{driver}/callback', [SocialController::class, 'callback']);
});
