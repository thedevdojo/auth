<?php

use Devdojo\Auth\Tests\Browser\Pages\Login;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Successful Login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->createJohnDoe()
            ->formLoginAsJohnDoe()
            ->assertRedirectAfterAuthUrlIsCorrect();
    });
});

test('Validation Error for Empty Fields', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->authAttributeRemove('#email', 'required')
            ->testValidationErrorOnSubmit('The email field is required')
            ->typeAndSubmit('@email-input', 'johndoe@gmail.com')
            ->waitFor('@password-input')
            ->authAttributeRemove('#password', 'required')
            ->testValidationErrorOnSubmit('The password field is required');

    });
});

test('Invalid Email', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->authAttributeChange('#email', 'type', 'text')
            ->type('@email-input', 'johndoe')
            ->testValidationErrorOnSubmit('The email field must be a valid email address');
    });
});

test('Incorrect Password', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->createJohnDoe()
            ->typeAndSubmit('@email-input', 'johndoe@gmail.com')
            ->waitFor('@password-input')
            ->type('@password-input', 'password123')
            ->testValidationErrorOnSubmit('These credentials do not match our records');
    });
});

test('Can Edit Email Address', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->typeAndSubmit('@email-input', 'canichange@myemail.com')
            ->waitFor('@edit-email-button')
            ->click('@edit-email-button')
            ->waitFor('@email-input')
            ->typeAndSubmit('@email-input', 'yesicanchange@myemail.com')
            ->waitFor('@email-read-only-placeholder')
            ->assertSeeIn('@email-read-only-placeholder', 'yesicanchange@myemail.com');
    });
});

test('Link to Register Page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->click('@register-link')
            ->waitFor('@auth-register')
            ->assertPathIs('/auth/register');

    });
});

test('Link to Forgot Password Page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->typeAndSubmit('@email-input', 'testingForgotPassword@gmail.com')
            ->waitFor('@forgot-password-link')
            ->click('@forgot-password-link')
            ->waitFor('@auth-password-reset')
            ->assertPathIs('/auth/password/reset');

    });
});

/* ---------------------------------------------------------
* Here are a few more that we'll need to add once
* we have reveal password functionality and remember me
------------------------------------------------------------ */

test('Verify Password Visibility Toggle', function () {
    $this->browse(function (Browser $browser) {
        // Test here
    });
})->todo();

test('Verify Remember Me Functionality', function () {
    $this->browse(function (Browser $browser) {
        // Test here
    });
})->todo();

/* ---------------------------------------------------------
* Investigate further how we can do an accessiblity check
------------------------------------------------------------ */

test('Verify Page Accessibility', function () {
    $this->browse(function (Browser $browser) {
        // Test here
    });
})->todo();
