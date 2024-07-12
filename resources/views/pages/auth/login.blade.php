<?php

use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Devdojo\Auth\Traits\HasConfigs;

if(!isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || !app()->isLocal()){
    middleware(['guest']);
}

name('auth.login');

new class extends Component
{
    use HasConfigs;

    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public $showPasswordField = false;

    public $showIdentifierInput = true;
    public $showSocialProviderInfo = false;

    public $language = [];

    public $twoFactorEnabled = true;

    public $userSocialProviders = [];

    public $userModel = null;

    public function mount(){
        $this->loadConfigs();
        $this->twoFactorEnabled = $this->settings->enable_2fa;
        $this->userModel = app(config('auth.providers.users.model'));
    }

    public function editIdentity(){
        if($this->showPasswordField){
            $this->showPasswordField = false;
            return;
        }
        
        $this->showIdentifierInput = true;
        $this->showSocialProviderInfo = false;
    }

    public function authenticate()
    {

        if(!$this->showPasswordField){
            $this->validateOnly('email');
            $userTryingToValidate = $this->userModel->where('email', $this->email)->first();
            if(!is_null($userTryingToValidate)){
                if(is_null($userTryingToValidate->password)){
                    $this->userSocialProviders = [];
                    // User is attempting to login and password is null. Need to show Social Provider info
                    foreach($userTryingToValidate->socialProviders->all() as $provider){
                        array_push($this->userSocialProviders, $provider->provider_slug);
                    }
                    $this->showIdentifierInput = false;
                    $this->showSocialProviderInfo = true;
                    return;
                }
            }
            $this->showPasswordField = true;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");
            return;
        }


        $this->validate();

        $credentials = ['email' => $this->email, 'password' => $this->password];

        if(!\Auth::validate($credentials)){
            $this->addError('password', trans('auth.failed'));
            return;
        }

        $userAttemptingLogin = $this->userModel->where('email', $this->email)->first();

        if(!isset($userAttemptingLogin->id)){
            $this->addError('password', trans('auth.failed'));
            return;
        }

        if($this->twoFactorEnabled && !is_null($userAttemptingLogin->two_factor_confirmed_at)){
            // We want this user to login via 2fa
            session()->put([
                'login.id' => $userAttemptingLogin->getKey()
            ]);

            return redirect()->route('auth.two-factor-challenge');

        } else {
            if (!Auth::attempt($credentials)) {
                $this->addError('password', trans('auth.failed'));
                return;
            }
            
            event(new Login(auth()->guard('web'), $this->userModel->where('email', $this->email)->first(), true));

            if(session()->get('url.intended') != route('logout.get')){
                session()->regenerate();
                redirect()->intended(config('devdojo.auth.settings.redirect_after_auth'));
            } else {
                session()->regenerate();
                return redirect(config('devdojo.auth.settings.redirect_after_auth'));
            }
        }

    }
};

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.login.page_title') }}">
    
    @volt('auth.login')
        <x-auth::elements.container>

            <x-auth::elements.heading
                :text="($language->login->headline ?? 'No Heading')"
                :description="($language->login->subheadline ?? 'No Description')"
                :show_subheadline="($language->login->show_subheadline ?? false)" />

            <x-auth::elements.session-message />

            @if(config('devdojo.auth.settings.login_show_social_providers') && config('devdojo.auth.settings.social_providers_location') == 'top')
                <x-auth::elements.social-providers />
            @endif

            <form wire:submit="authenticate" class="space-y-5">

                @if($showPasswordField)
                    <x-auth::elements.input-placeholder value="{{ $email }}">
                        <button type="button" data-auth="edit-email-button" wire:click="editIdentity" class="font-medium text-blue-500">{{ config('devdojo.auth.language.login.edit') }}</button>
                    </x-auth::elements.input-placeholder>
                @else
                    @if($showIdentifierInput)
                        <x-auth::elements.input :label="config('devdojo.auth.language.login.email_address')" type="email" wire:model="email" autofocus="true" data-auth="email-input" id="email" required />
                    @endif
                @endif

                @if($showSocialProviderInfo)
                    <div class="p-4 text-sm rounded-md border bg-zinc-50 border-zinc-200">
                        <span>{{ str_replace('__social_providers_list__', implode(', ', $userSocialProviders), config('devdojo.auth.language.login.social_auth_authenticated_message')) }}</span>
                        <button wire:click="editIdentity" type="button" class="underline translate-x-0.5">{{ config('devdojo.auth.language.login.change_email') }}</button>
                    </div>

                    @if(!config('devdojo.auth.settings.login_show_social_providers'))
                        <x-auth::elements.social-providers
                            :socialProviders="\Devdojo\Auth\Helper::getProvidersFromArray($userSocialProviders)"
                            :separator="false"
                        />
                    @endif
                @endif

                @if($showPasswordField)
                    <x-auth::elements.input :label="config('devdojo.auth.language.login.password')" type="password" wire:model="password" id="password" data-auth="password-input" />
                    <div class="flex justify-between items-center mt-6 text-sm leading-5">
                        <x-auth::elements.text-link href="{{ route('auth.password.request') }}" data-auth="forgot-password-link">{{ config('devdojo.auth.language.login.forget_password') }}</x-auth::elements.text-link>
                    </div>
                @endif

                <x-auth::elements.button type="primary" data-auth="submit-button" rounded="md" size="md" submit="true">
                    {{ config('devdojo.auth.language.login.button') }}
                </x-auth::elements.button>
            </form>


            <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
                <span class="opacity-[47%]"> {{ config('devdojo.auth.language.login.dont_have_an_account') }} </span>
                <x-auth::elements.text-link data-auth="register-link" href="{{ route('auth.register') }}">{{ config('devdojo.auth.language.login.sign_up') }}</x-auth::elements.text-link>
            </div>

            @if(config('devdojo.auth.settings.login_show_social_providers') && config('devdojo.auth.settings.social_providers_location') != 'top')
                <x-auth::elements.social-providers />
            @endif

        </x-auth::elements.container>
    @endvolt
    
</x-auth::layouts.app>