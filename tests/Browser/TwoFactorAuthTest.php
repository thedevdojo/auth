<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Redirected to login if not authenticated', function () {
    visit('/user/two-factor-authentication')
        ->assertPathIs('/auth/login');
});

test('Successfully View 2FA Setup Page', function () {
    setAuthConfig('devdojo.auth.settings.enable_2fa', true);

    createJohnDoe();
    loginAsJohnDoe();

    visit('/user/two-factor-authentication')
        ->assertSee('Two factor authentication disabled')
        ->click(authSelector('@enable-button'))
        ->assertSee('Finish enabling two factor authentication');

    resetAuthConfig();
});
