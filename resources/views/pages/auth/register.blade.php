<?php

use Devdojo\Auth\Traits\HasConfigs;
use Devdojo\Auth\Traits\RegistersUsers;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

if (! isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || ! app()->isLocal()) {
    middleware(['guest']);
}

name('auth.register');

// The flow itself lives in the RegistersUsers trait so an app can wear it
// on its own component (a modal, an inline form); this page is the view.
new class extends Component
{
    use HasConfigs, RegistersUsers;

    public function mount()
    {
        $this->loadConfigs();

        if (! $this->settings->registration_enabled) {
            session()->flash('error', config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.'));

            return redirect()->route('auth.login');
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
