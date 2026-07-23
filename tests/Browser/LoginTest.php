<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Successful Login', function () {
    createJohnDoe();

    $page = visit('/auth/login');

    formLoginAsJohnDoe($page);

    assertRedirectAfterAuthUrlIsCorrect($page);
});

test('Validation Error for Empty Fields', function () {
    $page = visit('/auth/login');

    authAttributeRemove($page, '#email', 'required');
    testValidationErrorOnSubmit($page, 'The email field is required');
    typeAndSubmit($page, '@email-input', 'johndoe@gmail.com');
    $page->assertPresent(authSelector('@password-input'));
    authAttributeRemove($page, '#password', 'required');
    testValidationErrorOnSubmit($page, 'The password field is required');
});

test('Invalid Email', function () {
    $page = visit('/auth/login');

    authAttributeChange($page, '#email', 'type', 'text');
    $page->fill(authSelector('@email-input'), 'johndoe');
    testValidationErrorOnSubmit($page, 'The email field must be a valid email address');
});

test('Incorrect Password', function () {
    createJohnDoe();

    $page = visit('/auth/login');

    typeAndSubmit($page, '@email-input', 'johndoe@gmail.com');
    $page->fill(authSelector('@password-input'), 'password123');
    testValidationErrorOnSubmit($page, 'These credentials do not match our records');
});

test('Can Edit Email Address', function () {
    $page = visit('/auth/login');

    typeAndSubmit($page, '@email-input', 'canichange@myemail.com');
    $page->click(authSelector('@edit-email-button'))
        ->fill(authSelector('@email-input'), 'yesicanchange@myemail.com')
        ->click(authSelector('@submit-button'))
        ->assertSeeIn(authSelector('@email-read-only-placeholder'), 'yesicanchange@myemail.com');
});

test('Link to Register Page', function () {
    $page = visit('/auth/login');

    $page->click(authSelector('@register-link'))
        ->assertPresent(authSelector('@auth-register'))
        ->assertPathIs('/auth/register');
});

test('Link to Forgot Password Page', function () {
    $page = visit('/auth/login');

    typeAndSubmit($page, '@email-input', 'testingForgotPassword@gmail.com');
    $page->click(authSelector('@forgot-password-link'))
        ->assertPresent(authSelector('@auth-password-reset'))
        ->assertPathIs('/auth/password/reset');
});

test('Verify Password Visibility Toggle', function () {
    visit('/auth/login');
})->todo();

test('Verify Remember Me Functionality', function () {
    visit('/auth/login');
})->todo();

test('Verify Page Accessibility', function () {
    visit('/auth/login');
})->todo();
