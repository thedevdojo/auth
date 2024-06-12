<?php

use Devdojo\Auth\Tests\Browser\Pages\Login;
use Devdojo\Auth\Tests\Browser\Pages\TwoFactorChallenge;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Redirected to login if not authenticated', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit(new TwoFactorChallenge)
            ->assertPathIs('/auth/login');
    });
});

test('User logs in with 2fa enabled and redirected to challenge', function () {
    // Turn on 2FA site-wide
    $this->setConfig('devdojo.auth.settings.enable_2fa', true);
    $this->browse(function (Browser $browser) {
        $browser->visit(new Login)
            ->createJohnDoe()
            ->enable2FAforJohnDoe()
            ->formLoginAsJohnDoe()
            ->assertPathIs('/auth/two-factor-challenge');
    });
    $this->resetConfig();
});
