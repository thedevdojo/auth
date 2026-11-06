<?php

namespace Devdojo\Auth\Traits;

use Devdojo\Auth\Rules\PasswordStrength;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * The registration flow, as a trait any Livewire component can wear.
 *
 * The package's own `auth/register` page uses it, and so can an app's
 * modal or inline form — the state names (`name`, `email`, `password`,
 * `showNameField`, `showPasswordField`…) are the page's. Every
 * `registration_*` setting is honoured here; the hooks at the bottom
 * (`registrationData()`, `createUser()`, `registered()`) are the seams
 * an app overrides to shape the account or the landing.
 */
trait RegistersUsers
{
    public $name;

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $showNameField = false;

    public $showEmailField = true;

    public $showPasswordField = false;

    public $showPasswordConfirmationField = false;

    public $showEmailRegistration = true;

    public function rules()
    {
        if (! config('devdojo.auth.settings.enable_email_registration')) {
            return [];
        }

        $nameValidationRules = [];
        if (config('devdojo.auth.settings.registration_include_name_field')) {
            $nameValidationRules = ['name' => 'required'];
        }

        $includeConfirmation = config('devdojo.auth.settings.registration_include_password_confirmation_field');
        $passwordValidationRules = ['password' => PasswordStrength::rules($includeConfirmation)];

        return array_merge(
            $nameValidationRules,
            ['email' => 'required|email|unique:users'],
            $passwordValidationRules,
        );
    }

    public function mountRegistersUsers(): void
    {
        if (! config('devdojo.auth.settings.enable_email_registration')) {
            $this->showEmailRegistration = false;
            $this->showNameField = false;
            $this->showEmailField = false;
            $this->showPasswordField = false;
            $this->showPasswordConfirmationField = false;

            return;
        }

        if (config('devdojo.auth.settings.registration_include_name_field')) {
            $this->showNameField = true;
        }

        if (config('devdojo.auth.settings.registration_show_password_same_screen')) {
            $this->showPasswordField = true;

            if (config('devdojo.auth.settings.registration_include_password_confirmation_field')) {
                $this->showPasswordConfirmationField = true;
            }
        }
    }

    public function register()
    {
        if (! config('devdojo.auth.settings.registration_enabled')) {
            session()->flash('error', config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.'));

            return redirect()->route('auth.login');
        }

        if (! config('devdojo.auth.settings.enable_email_registration')) {
            session()->flash('error', config('devdojo.auth.language.register.email_registration_disabled', 'Email registration is currently disabled. Please use social login.'));

            return redirect()->route('auth.register');
        }

        if (! $this->showPasswordField) {
            if (config('devdojo.auth.settings.registration_include_name_field')) {
                $this->validateOnly('name');
            }
            $this->validateOnly('email');

            $this->showPasswordField = true;
            if (config('devdojo.auth.settings.registration_include_password_confirmation_field')) {
                $this->showPasswordConfirmationField = true;
            }
            $this->showNameField = false;
            $this->showEmailField = false;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");

            return;
        }

        $this->validate();

        $user = $this->createUser($this->registrationData());

        event(new Registered($user));

        Auth::login($user, true);

        return $this->registered($user);
    }

    /** The attributes the new account is created with. */
    protected function registrationData(): array
    {
        $data = [
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ];

        if (config('devdojo.auth.settings.registration_include_name_field')) {
            $data['name'] = $this->name;
        }

        return $data;
    }

    protected function createUser(array $data)
    {
        return app(config('auth.providers.users.model'))->create($data);
    }

    /** Where a freshly registered user goes. Override for a different landing. */
    protected function registered($user)
    {
        if (config('devdojo.auth.settings.registration_require_email_verification')) {
            return redirect()->route('verification.notice');
        }

        session()->regenerate();

        $to = config('devdojo.auth.settings.redirect_after_auth');

        if (session()->get('url.intended') != route('logout.get')) {
            return redirect()->intended($to);
        }

        return redirect($to);
    }
}
