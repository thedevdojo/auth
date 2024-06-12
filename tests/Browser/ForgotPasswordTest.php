<?php

/**
 * This is for the /auth/password/reset and /auth/password/hAsHeDsTrInG page
 */

use Devdojo\Auth\Tests\Browser\Pages\PasswordResetRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Empty Email Validation', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->authAttributeRemove('#email', 'required')
            ->testValidationErrorOnSubmit('The email field is required');
    });
});

test('Invalid Email Validation', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->authAttributeChange('#email', 'type', 'text')
            ->type('@email-input', 'johndoe')
            ->testValidationErrorOnSubmit('The email field must be a valid email address');
    });
});

test('Email Does Not Exist', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->type('@email-input', 'jimmycrackcorn@gmail.com')
            ->testValidationErrorOnSubmit('We can\'t find a user with that email address');
    });
});

test('Email reset functionality', function () {
    $browser = $this->browse(function (Browser $browser) {

        $browser
            ->visit(new PasswordResetRequest)
            ->createJohnDoe()
            ->clearLogFile()
            ->typeAndSubmit('@email-input', 'johndoe@gmail.com')
            ->waitForText('We have emailed your password reset link')
            ->getLogFile(function ($content) use ($browser) {

                $foundLine = $this->findLineContainingSubstring($content, 'Reset Password:');
                $url = str_replace('Reset Password: ', '', $foundLine);
                $browser
                    ->visit($url)
                    ->assertSeeIn('#auth-heading-title', 'Reset Password')
                    ->type('@password-input', 'password123')
                    ->type('@password-confirm-input', 'password123')
                    ->clickAndWaitForReload('@submit-button')
                    ->assertRedirectAfterAuthUrlIsCorrect();
            });
    });
});

test('Link to Login Page', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->click('@login-link')
            ->waitFor('@auth-login')
            ->assertPathIs('/auth/login');
    });
});
