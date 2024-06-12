<?php

/**
 * This is for the /auth/password/confirm page
 */

use Devdojo\Auth\Tests\Browser\Pages\ConfirmPassword;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Guest redirect to login page', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new ConfirmPassword)
            ->assertPathIs('/auth/login');
    });
});

test('Password Confirm Protected Page Redirects', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visitPasswordConfirmTestPage()
            ->assertPathIs('/auth/login')
            ->createJohnDoe()
            ->loginAsJohnDoe()
            ->visitPasswordConfirmTestPage()
            ->assertPathIs('/auth/password/confirm');
    });
});

test('Password Confirm Works', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->createJohnDoe()
            ->loginAsJohnDoe()
            ->visit(new ConfirmPassword)
            ->type('@password-input', 'password')
            ->clickAndWaitForReload('@submit-button')
            ->assertRedirectAfterAuthUrlIsCorrect();
    });
});

test('Password Confirm Works and redirect to password protected page', function () {
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->createJohnDoe()
            ->loginAsJohnDoe()
            ->visitPasswordConfirmTestPage()
            ->type('@password-input', 'password')
            ->clickAndWaitForReload('@submit-button')
            ->assertSee('Test Confirmed');
    });
});
