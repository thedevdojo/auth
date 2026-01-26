<?php

use Devdojo\Auth\Rules\PasswordStrength;
use Devdojo\Auth\Traits\HasConfigs;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

if (! isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || ! app()->isLocal()) {
    middleware(['guest']);
}

name('auth.register');

new class extends Component
{
    use HasConfigs;

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
        if (! $this->settings->enable_email_registration) {
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

    public function mount()
    {
        $this->loadConfigs();

        if (! $this->settings->registration_enabled) {
            session()->flash('error', config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.'));
            redirect()->route('auth.login');

            return;
        }

        if (! $this->settings->enable_email_registration) {
            $this->showEmailRegistration = false;
            $this->showNameField = false;
            $this->showEmailField = false;
            $this->showPasswordField = false;
            $this->showPasswordConfirmationField = false;

            return;
        }

        if ($this->settings->registration_include_name_field) {
            $this->showNameField = true;
        }

        if ($this->settings->registration_show_password_same_screen) {
            $this->showPasswordField = true;

            if ($this->settings->registration_include_password_confirmation_field) {
                $this->showPasswordConfirmationField = true;
            }
        }
    }

    public function register()
    {
        if (! $this->settings->registration_enabled) {
            session()->flash('error', config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.'));

            return redirect()->route('auth.login');
        }

        if (! $this->settings->enable_email_registration) {
            session()->flash('error', config('devdojo.auth.language.register.email_registration_disabled', 'Email registration is currently disabled. Please use social login.'));

            return redirect()->route('auth.register');
        }

        if (! $this->showPasswordField) {
            if ($this->settings->registration_include_name_field) {
                $this->validateOnly('name');
            }
            $this->validateOnly('email');

            $this->showPasswordField = true;
            if ($this->settings->registration_include_password_confirmation_field) {
                $this->showPasswordConfirmationField = true;
            }
            $this->showNameField = false;
            $this->showEmailField = false;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");

            return;
        }

        $this->validate();

        $userData = [
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ];

        if ($this->settings->registration_include_name_field) {
            $userData['name'] = $this->name;
        }

        $user = app(config('auth.providers.users.model'))->create($userData);

        event(new Registered($user));

        Auth::login($user, true);

        if (config('devdojo.auth.settings.registration_require_email_verification')) {
            return redirect()->route('verification.notice');
        }

        if (session()->get('url.intended') != route('logout.get')) {
            session()->regenerate();
            redirect()->intended(config('devdojo.auth.settings.redirect_after_auth'));
        } else {
            session()->regenerate();

            return redirect(config('devdojo.auth.settings.redirect_after_auth'));
        }
    }
};

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.register.page_title') }}">

    @volt('auth.register')
    <x-auth::elements.container>

        <x-auth::elements.heading :text="($language->register->headline ?? 'No Heading')" :description="($language->register->subheadline ?? 'No Description')" :show_subheadline="($language->register->show_subheadline ?? false)" />
        <x-auth::elements.session-message />

        @if(config('devdojo.auth.settings.social_providers_location') == 'top')
            <x-auth::elements.social-providers :separator="$showEmailRegistration" />
        @endif

        @if($showEmailRegistration)
        <form wire:submit="register" class="space-y-5">

            @if($showNameField)
            <x-auth::elements.input :label="config('devdojo.auth.language.register.name')" type="text" wire:model="name" autofocus="true" required />
            @endif

            @if($showEmailField)
            @php
            $autofocusEmail = ($showNameField) ? false : true;
            @endphp
            <x-auth::elements.input :label="config('devdojo.auth.language.register.email_address')" id="email" name="email" type="email" wire:model="email" data-auth="email-input" :autofocus="$autofocusEmail" autocomplete="email" required />
            @endif

            @if($showPasswordField)
           <x-auth::elements.input :label="config('devdojo.auth.language.register.password')" type="password" wire:model="password" id="password" name="password" data-auth="password-input" autocomplete="new-password" required />
            <x-auth::elements.password-requirements />
            @endif

            @if($showPasswordConfirmationField)
            <x-auth::elements.input :label="config('devdojo.auth.language.register.password_confirmation')" type="password" wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" data-auth="password-confirmation-input" autocomplete="new-password" required />
            @endif

            <x-auth::elements.button data-auth="submit-button" rounded="md" submit="true">{{config('devdojo.auth.language.register.button')}}</x-auth::elements.button>
        </form>
        @endif

        <div class="@if(config('devdojo.auth.settings.social_providers_location') != 'top' && $showEmailRegistration){{ 'mt-3' }}@endif space-x-0.5 text-sm leading-5 @if(config('devdojo.auth.settings.center_align_text')){{ 'text-center' }}@else{{ 'text-left' }}@endif" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
            <span class="opacity-47">{{config('devdojo.auth.language.register.already_have_an_account')}}</span>
            <x-auth::elements.text-link data-auth="login-link" href="{{ route('auth.login') }}">{{config('devdojo.auth.language.register.sign_in')}}</x-auth::elements.text-link>
        </div>

        @if(config('devdojo.auth.settings.social_providers_location') != 'top')
            <x-auth::elements.social-providers :separator="$showEmailRegistration" />
        @endif


    </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>
