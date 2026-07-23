<?php

/**
 * This is for the /auth/password/reset and /auth/password/hAsHeDsTrInG page
 */

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Empty Email Validation', function () {
    $page = visit('/auth/password/reset');

    authAttributeRemove($page, '#email', 'required');
    testValidationErrorOnSubmit($page, 'The email field is required');
});

test('Invalid Email Validation', function () {
    $page = visit('/auth/password/reset');

    authAttributeChange($page, '#email', 'type', 'text');
    $page->fill(authSelector('@email-input'), 'johndoe');
    testValidationErrorOnSubmit($page, 'The email field must be a valid email address');
});

test('Email Does Not Exist', function () {
    $page = visit('/auth/password/reset');

    $page->fill(authSelector('@email-input'), 'jimmycrackcorn@gmail.com');
    testValidationErrorOnSubmit($page, 'We can\'t find a user with that email address');
});

test('Email reset functionality', function () {
    createJohnDoe();
    clearLogFile();

    typeAndSubmit(visit('/auth/password/reset'), '@email-input', 'johndoe@gmail.com')
        ->assertSee('We have emailed your password reset link');

    $content = file_get_contents(storage_path('logs/laravel.log'));
    $foundLine = getLogLineContaining($content, 'Reset Password:');
    $url = str_replace('Reset Password: ', '', (string) $foundLine);

    $page = visit($url)
        ->assertSeeIn('#auth-heading-title', 'Reset Password')
        ->fill(authSelector('@password-input'), 'password123')
        ->fill(authSelector('@password-confirm-input'), 'password123')
        ->click(authSelector('@submit-button'));

    assertRedirectAfterAuthUrlIsCorrect($page);
});

test('Link to Login Page', function () {
    visit('/auth/password/reset')
        ->click(authSelector('@login-link'))
        ->assertPresent(authSelector('@auth-login'))
        ->assertPathIs('/auth/login');
});
