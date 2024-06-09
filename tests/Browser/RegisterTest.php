<?php

use Devdojo\Auth\Tests\Browser\Pages\Register;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Successful Registration', function () {

    $this->browse(function (Browser $browser) {
        $browser->visit(new Register)
            ->registerAsJohnDoe()
            ->assertRedirectAfterAuthUrlIsCorrect();
    });
});

test('Validation Error for Empty Fields', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Register)
            ->authAttributeRemove('#email', 'required')
            ->authAttributeRemove('#password', 'required')
            ->testValidationErrorOnSubmit('The email field is required')
            ->assertSee('The password field is required');
    });
});

test('Invalid Email Address', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Register)
            ->authAttributeChange('#email', 'type', 'text')
            ->authAttributeRemove('#password', 'required')
            ->type('@email-input', 'johndoe')
            ->testValidationErrorOnSubmit('The email field must be a valid email address');
    });
});

test('Invalid Password', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Register)
            ->type('@email-input', 'johndoe@gmail.com')
            ->type('@password-input', 'pass')
            ->testValidationErrorOnSubmit('The password field must be at least 8 characters');
    });
});

test('Email already taken', function () {
    $this->browse(function (Browser $browser) {
        $browser
            ->visit(new Register)
            ->createJohnDoe()
            ->type('@email-input', 'johndoe@gmail.com')
            ->type('@password-input', 'password')
            ->testValidationErrorOnSubmit('The email has already been taken');
    });
});

test('Return to Login', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new Register)
            ->click('@login-link')
            ->waitFor('@auth-login')
            ->assertPathIs('/auth/login');
    });
});

// Add more tests to test when the Name field is shown on the register page, or if the user keeps the password on a separate screen
