<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Successful Registration', function () {
    $page = visit('/auth/register');

    registerAsJohnDoe($page);

    assertRedirectAfterAuthUrlIsCorrect($page);
});

test('Validation Error for Empty Fields', function () {
    $page = visit('/auth/register');

    authAttributeRemove($page, '#email', 'required');
    authAttributeRemove($page, '#password', 'required');
    testValidationErrorOnSubmit($page, 'The email field is required');
    $page->assertSee('The password field is required');
});

test('Invalid Email Address', function () {
    $page = visit('/auth/register');

    authAttributeChange($page, '#email', 'type', 'text');
    authAttributeRemove($page, '#password', 'required');
    $page->fill(authSelector('@email-input'), 'johndoe');
    testValidationErrorOnSubmit($page, 'The email field must be a valid email address');
});

test('Invalid Password', function () {
    $page = visit('/auth/register');

    $page->fill(authSelector('@email-input'), 'johndoe@gmail.com')
        ->fill(authSelector('@password-input'), 'pass');
    testValidationErrorOnSubmit($page, 'The password field must be at least 8 characters');
});

test('Email already taken', function () {
    createJohnDoe();

    $page = visit('/auth/register');

    $page->fill(authSelector('@email-input'), 'johndoe@gmail.com')
        ->fill(authSelector('@password-input'), 'password');
    testValidationErrorOnSubmit($page, 'The email has already been taken');
});

test('Return to Login', function () {
    $page = visit('/auth/register');

    $page->click(authSelector('@login-link'))
        ->assertPresent(authSelector('@auth-login'))
        ->assertPathIs('/auth/login');
});
