<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;

name('user.two-factor-authentication');
//middleware(['auth', 'verified', 'password.confirm']); 
middleware(['auth', 'verified']);
// middleware(['auth'])

new class extends Component
{
    public $showingConfirmation = false;
    
    public function mount(){
        
    }

}

?>

<x-auth::layouts.empty title="Two Factor Authentication">
    @volt('user.two-factor-authentication')
        <div class="flex mx-auto w-full max-w-xl">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Finish enabling two factor authentication.') }}
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <p>
                    {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                </p>
            </div>

            <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <p class="font-semibold">
                    @if ($showingConfirmation)
                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                    @else
                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                    @endif
                </p>
            </div>

            <div class="inline-block p-2 mt-4 bg-white">
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            </div>

            <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <p class="font-semibold">
                    {{ __('Setup Key') }}: {{ decrypt(auth()->user()->two_factor_secret) }}
                </p>
            </div>

            @if ($showingConfirmation)
                <div class="mt-4">
                    <label>code</label>

                    <x-auth::elements.input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                        wire:model="code"
                        wire:keydown.enter="confirmTwoFactorAuthentication" />
                </div>
            @endif

            @if ($showingRecoveryCodes ?? false)
                <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-semibold">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </p>
                </div>

                <div class="grid gap-1 px-4 py-4 mt-4 max-w-xl font-mono text-sm bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-100">
                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif

        </div>
    @endvolt

</x-auth::layouts.empty>