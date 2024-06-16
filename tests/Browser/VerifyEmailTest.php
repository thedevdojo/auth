<?php

use Devdojo\Auth\Tests\Browser\Pages\Register;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

test('Email Verification on Registration', function () {
    // Turn on Require Email Validation before running tests
    $this->setConfig('devdojo.auth.settings.registration_require_email_verification', true);
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new Register)
            ->disableFitOnFailure()
            ->clearLogFile()
            ->registerAsJohnDoe()
            ->assertPathIs('/auth/verify')
            ->getLogFile(function ($content) use ($browser) {

                $foundLine = $this->findLineContainingSubstring($content, 'Verify Email Address:');
                $url = str_replace('Verify Email Address: ', '', $foundLine);
                $browser
                    ->visit($url)
                    ->assertRedirectAfterAuthUrlIsCorrect();
            });

    });
    $this->resetConfig();
});

test('Resend Email Verification Link', function () {
    // Turn on Require Email Validation before running tests
    $this->setConfig('devdojo.auth.settings.registration_require_email_verification', true);
    $browser = $this->browse(function (Browser $browser) {
        $browser
            ->visit(new Register)
            ->disableFitOnFailure()
            ->registerAsJohnDoe()
            ->assertPathIs('/auth/verify')
            ->clearLogFile()
            ->click('@verify-email-resend-link')
            ->waitForText(config('devdojo.auth.language.verify.new_link_sent'))
            ->getLogFile(function ($content) use ($browser) {
                $foundLine = $this->findLineContainingSubstring($content, 'Verify Email Address:');
                $url = str_replace('Verify Email Address: ', '', $foundLine);
                $browser
                    ->visit($url)
                    ->assertRedirectAfterAuthUrlIsCorrect();
            });

    });
    $this->resetConfig();

});
