<?php

namespace Devdojo\Auth\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Dusk;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        Dusk::selectorHtmlAttribute('data-auth');

        Browser::macro('authAttributeChange', function (?string $selector, string $attribute, string $value) {
            $this->script("document.querySelector('$selector').setAttribute('$attribute', '$value');");

            return $this;
        });

        Browser::macro('authAttributeRemove', function (?string $selector, string $attribute) {
            $this->script("document.querySelector('$selector').removeAttribute('$attribute');");

            return $this;
        });

        Browser::macro('testValidationErrorOnSubmit', function (string $message = '') {
            $this
                ->click('@submit-button')
                ->waitForText($message)
                ->assertSee($message);

            return $this;
        });

        Browser::macro('createJohnDoe', function () {
            $user = \App\Models\User::factory()->create([
                'email' => 'johndoe@gmail.com',
                'password' => \Hash::make('password'),
            ]);

            return $this;
        });

        Browser::macro('assertRedirectAfterAuthUrlIsCorrect', function () {
            $redirectExpectedToBe = '/';
            if (class_exists(\Devdojo\Genesis\Genesis::class)) {
                $redirectExpectedToBe = '/dashboard';
            }
            $this->assertPathIs($redirectExpectedToBe);

            return $this;
        });
        Config::set('devdojo.auth.settings.registration_require_email_verification', false);
    }
}
