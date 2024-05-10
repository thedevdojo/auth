<?php

use Devdojo\Auth\Http\Controllers\SocialController;
use Devdojo\Auth\Http\Controllers\VerifyEmailController;
use Devdojo\Auth\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;


use Devdojo\Auth\Actions\TwoFactorAuth\GenerateNewRecoveryCodes;
use Devdojo\Auth\Actions\TwoFactorAuth\GenerateQrCodeAndSecretKey;

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


    Route::get('g2fa', function(){
        $QrCodeAndSecret = new GenerateQrCodeAndSecretKey();
        [$qr, $secret] = $QrCodeAndSecret(auth()->user());
        echo '<img src="data:image/png;base64, ' . $qr . ' " style="width:400px; height:auto" />';
        // $secret should be saved to user database as two_factor_secret, but it should be encrypted like `encrypt($secret)`

    });

    Route::get('getr', function(){
        $generateCodesFor = new GenerateNewRecoveryCodes();
        $generateCodesFor(auth()->user());
    });

    Route::get('newr', function(){
        dd(auth()->user()->hasEnabledTwoFactorAuthentication());
    });

});


Route::middleware(['web'])->group(function () {
    // Add social routes
    Route::get('auth/{driver}/redirect', [SocialController::class, 'redirect']);
    Route::get('auth/{driver}/callback', [SocialController::class, 'callback']);
});