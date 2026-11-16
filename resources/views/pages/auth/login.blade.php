<?php

use Devdojo\Auth\Traits\AuthenticatesUsers;
use Devdojo\Auth\Traits\HasConfigs;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

if (! isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || ! app()->isLocal()) {
    middleware(['guest']);
}

name('auth.login');

// The flow itself lives in the AuthenticatesUsers trait so an app can wear
// it on its own component (a modal, an inline form); this page is the view.
new class extends Component
{
    use AuthenticatesUsers, HasConfigs;

    public function mount()
    {
        $this->loadConfigs();
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
                        <x-auth::elements.input :label="config('devdojo.auth.language.login.email_address')" type="email" wire:model="email" autofocus="true" data-auth="email-input" id="email" name="email" autocomplete="email" required />
                    @endif
                @endif

                @if($showSocialProviderInfo)
                    <div class="p-4 text-sm border rounded-md bg-zinc-50 border-zinc-200">
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

                @php
                    $passwordFieldClasses = $showPasswordField ? 'flex flex-col gap-6' : 'hidden';
                @endphp

                <div class="{{ $passwordFieldClasses }}">
                    <x-auth::elements.input :label="config('devdojo.auth.language.login.password')" type="password" wire:model="password" data-auth="password-input" id="password" name="password" autocomplete="current-password" />
                    <x-auth::elements.checkbox :label="config('devdojo.auth.language.login.remember_me')" wire:model="rememberMe" id="remember-me" data-auth="remember-me-input" />
                    <div class="flex items-center justify-between text-sm leading-5">
                        <x-auth::elements.text-link href="{{ route('auth.password.request') }}" data-auth="forgot-password-link">{{ config('devdojo.auth.language.login.forget_password') }}</x-auth::elements.text-link>
                    </div>
                </div>

                <x-auth::elements.button type="primary" data-auth="submit-button" rounded="md" size="md" submit="true">
                    {{ config('devdojo.auth.language.login.button') }}
                </x-auth::elements.button>
            </form>


            @if(config('devdojo.auth.settings.registration_enabled', true))
                <div class="mt-3 space-x-0.5 text-sm leading-5 @if(config('devdojo.auth.settings.center_align_text')){{ 'text-center' }}@else{{ 'text-left' }}@endif" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
                    <span class="opacity-47"> {{ config('devdojo.auth.language.login.dont_have_an_account') }} </span>
                    <x-auth::elements.text-link data-auth="register-link" href="{{ route('auth.register') }}">{{ config('devdojo.auth.language.login.sign_up') }}</x-auth::elements.text-link>
                </div>
            @endif

            @if(config('devdojo.auth.settings.login_show_social_providers') && config('devdojo.auth.settings.social_providers_location') != 'top')
                <x-auth::elements.social-providers />
            @endif

        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>
