<?php

use Devdojo\Auth\Models\SocialProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;
use Devdojo\Auth\Traits\HasConfigs;
use function Laravel\Folio\{middleware, name};

if (!isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || !app()->isLocal()) {
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




    public function rules()
    {
        $nameValidationRules = [];
        if (config('devdojo.auth.settings.registration_include_name_field')) {
            $nameValidationRules = ['name' => 'required'];
        }

        $passwordValidationRules = ['password' => 'required|min:8'];
        if (config('devdojo.auth.settings.registration_include_password_confirmation_field')) {
            $passwordValidationRules['password'] .= '|confirmed';
        }
        return array_merge(
            $nameValidationRules,
            ['email' => 'required|email|unique:users'],
            $passwordValidationRules,
        );
    }

    public function mount()
    {
        $this->loadConfigs();

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
        if (!$this->showPasswordField) {
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
        <x-auth::elements.social-providers />
        @endif

        <form wire:submit="register" class="space-y-5">

            @if($showNameField)
            <x-auth::elements.input :label="config('devdojo.auth.language.register.name')" type="text" wire:model="name" autofocus="true" required />
            @endif

            @if($showEmailField)
            @php
            $autofocusEmail = ($showNameField) ? false : true;
            @endphp
            <x-auth::elements.input :label="config('devdojo.auth.language.register.email_address')" id="email" type="email" wire:model="email" data-auth="email-input" :autofocus="$autofocusEmail" required />
            @endif

            @if($showPasswordField)
            <x-auth::elements.input :label="config('devdojo.auth.language.register.password')" type="password" wire:model="password" id="password" data-auth="password-input" required />
            @endif

            @if($showPasswordConfirmationField)
            <x-auth::elements.input :label="config('devdojo.auth.language.register.password_confirmation')" type="password" wire:model="password_confirmation" id="password_confirmation" data-auth="password-confirmation-input" required />
            @endif

            <x-auth::elements.button data-auth="submit-button" rounded="md" submit="true">{{config('devdojo.auth.language.register.button')}}</x-auth::elements.button>
        </form>

        <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
            <span class="opacity-[47%]">{{config('devdojo.auth.language.register.already_have_an_account')}}</span>
            <x-auth::elements.text-link data-auth="login-link" href="{{ route('auth.login') }}">{{config('devdojo.auth.language.register.sign_in')}}</x-auth::elements.text-link>
        </div>

        @if(config('devdojo.auth.settings.social_providers_location') != 'top')
        <x-auth::elements.social-providers />
        @endif


    </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>