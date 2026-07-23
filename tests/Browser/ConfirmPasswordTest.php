<?php

/**
 * This is for the /auth/password/confirm page
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Guest redirect to login page', function () {
    visit('/auth/password/confirm')
        ->assertPathIs('/auth/login');
});

test('Password Confirm Protected Page Redirects', function () {
    visit('/auth/password_confirmation_test')
        ->assertPathIs('/auth/login');

    createJohnDoe();
    loginAsJohnDoe();

    visit('/auth/password_confirmation_test')
        ->assertPathIs('/auth/password/confirm');
});

test('Password Confirm Works', function () {
    createJohnDoe();
    loginAsJohnDoe();

    $page = visit('/auth/password/confirm')
        ->fill(authSelector('@password-input'), 'password')
        ->click(authSelector('@submit-button'));

    assertRedirectAfterAuthUrlIsCorrect($page);
});

test('Password Confirm Works and redirect to password protected page', function () {
    createJohnDoe();
    loginAsJohnDoe();

    visit('/auth/password_confirmation_test')
        ->assertPathIs('/auth/password/confirm')
        ->fill(authSelector('@password-input'), 'password')
        ->click(authSelector('@submit-button'))
        ->assertSee('Test Confirmed');
});
