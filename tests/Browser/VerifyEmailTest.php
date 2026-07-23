<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

test('Email Verification on Registration', function () {
    setAuthConfig('devdojo.auth.settings.registration_require_email_verification', true);

    clearLogFile();

    registerAsJohnDoe(visit('/auth/register'))
        ->assertPathIs('/auth/verify');

    $content = file_get_contents(storage_path('logs/laravel.log'));
    $foundLine = getLogLineContaining($content, 'Verify Email Address:');
    $url = str_replace('Verify Email Address: ', '', (string) $foundLine);

    assertRedirectAfterAuthUrlIsCorrect(visit($url));

    resetAuthConfig();
});

test('Resend Email Verification Link', function () {
    setAuthConfig('devdojo.auth.settings.registration_require_email_verification', true);

    registerAsJohnDoe(visit('/auth/register'))
        ->assertPathIs('/auth/verify');

    clearLogFile();

    visit('/auth/verify')
        ->click(authSelector('@verify-email-resend-link'))
        ->assertSee(config('devdojo.auth.language.verify.new_link_sent'));

    $content = file_get_contents(storage_path('logs/laravel.log'));
    $foundLine = getLogLineContaining($content, 'Verify Email Address:');
    $url = str_replace('Verify Email Address: ', '', (string) $foundLine);

    assertRedirectAfterAuthUrlIsCorrect(visit($url));

    resetAuthConfig();
});
