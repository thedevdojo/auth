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
})->todo();

test('Invalid Email Validation', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->authAttributeChange('#email', 'type', 'text')
            ->type('@email-input', 'johndoe')
            ->testValidationErrorOnSubmit('The email field must be a valid email address');
    });
})->todo();

test('Email Does Not Exist', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new PasswordResetRequest)
            ->type('@email-input', 'jimmycrackcorn@gmail.com')
            ->testValidationErrorOnSubmit('We can\'t find a user with that email address');
    });
})->todo();

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
                    ->pause(2000)
                    ->assertSeeIn('#auth-heading-title', 'Reset Password');
            });

            //->waitForText('We have emailed your password reset link');
            // ->assertPathIs('/auth/verify')
            
    });
});

test('Link to Login Page', function () {
    $browser = $this->browse(function (Browser $browser) {
        
    });
})->todo();