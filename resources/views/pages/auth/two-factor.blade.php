<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Devdojo\Auth\Traits\HasConfigs;


name('auth.two-factor');


new class extends Component
{

    /**
     * Indicates if two factor authentication QR code is being displayed.
     *
     * @var bool
     */
    public $showingQrCode = true;

    /**
     * Indicates if the two factor authentication confirmation input and button are being displayed.
     *
     * @var bool
     */
    public $showingConfirmation = true;

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming two factor authentication.
     *
     * @var string|null
     */
    public $code;

}

?>

<x-auth::layouts.empty title="{{ config('devdojo.auth.language.login.page_title') }}">
    @volt('auth.two-factor')
        <section class="flex justify-center items-center w-screen h-screen">
            <div class="relative">
                <h2>
                    {{ __('Two Factor Authentication') }}
                </h2>

                <p>
                    {{ __('Add additional security to your account using two factor authentication.') }}
                </p>

                <main>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        
                        @if ($showingConfirmation)
                            {{ __('Finish enabling two factor authentication.') }}
                        @else
                            {{ __('You have enabled two factor authentication.') }}
                        @endif
                        
                    </h3>

                    <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                        <p>
                            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                        </p>
                    </div>

                    
                    @if ($showingQrCode)
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
                                {{-- {{ __('Setup Key') }}: {{ decrypt(auth()->user()->two_factor_secret) }} --}}
                            </p>
                        </div>

                        @if ($showingConfirmation)
                            <div class="mt-4">
                                <label for="code" value="{{ __('Code') }}" />

                                <input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                                    wire:model="code"
                                    wire:keydown.enter="confirmTwoFactorAuthentication" />

                                @error('code')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    @endif

                    @if ($showingRecoveryCodes)
                        <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
                            <p class="font-semibold">
                                {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                            </p>
                        </div>

                        <div class="grid gap-1 px-4 py-4 mt-4 max-w-xl font-mono text-sm bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-100">
                            {{-- @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <div>{{ $code }}</div>
                            @endforeach --}}
                        </div>
                    @endif

                    <div class="mt-5">
                        
                        @if ($showingRecoveryCodes)
                            <x-confirms-password wire:then="regenerateRecoveryCodes">
                                <button class="me-3">
                                    {{ __('Regenerate Recovery Codes') }}
                                </button>
                            </x-confirms-password>
                        @elseif ($showingConfirmation)
                            <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                                <button type="button" class="me-3" wire:loading.attr="disabled">
                                    {{ __('Confirm') }}
                                </button>
                            </x-confirms-password>
                        @else
                            <x-confirms-password wire:then="showRecoveryCodes">
                                <button class="me-3">
                                    {{ __('Show Recovery Codes') }}
                                </button>
                            </x-confirms-password>
                        @endif

                        @if ($showingConfirmation)
                            <x-confirms-password wire:then="disableTwoFactorAuthentication">
                                <button wire:loading.attr="disabled">
                                    {{ __('Cancel') }}
                                </button>
                            </x-confirms-password>
                        @else
                            <x-confirms-password wire:then="disableTwoFactorAuthentication">
                                <button wire:loading.attr="disabled">
                                    {{ __('Disable') }}
                                </button>
                            </x-confirms-password>
                        @endif
                    </div>
                </main>
            </div>
        </section>
    @endvolt

</x-auth::layouts.empty>