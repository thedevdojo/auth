<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Redirected to login if not authenticated', function () {
    visit('/auth/two-factor-challenge')
        ->assertPathIs('/auth/login');
});

test('User logs in with 2fa enabled and redirected to challenge', function () {
    setAuthConfig('devdojo.auth.settings.enable_2fa', true);

    createJohnDoe();
    enable2FAforJohnDoe();

    $page = visit('/auth/login');

    formLoginAsJohnDoe($page);

    $page->assertPathIs('/auth/two-factor-challenge');

    resetAuthConfig();
});
