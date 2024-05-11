<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use PragmaRX\Google2FA\Google2FA;
use Devdojo\Auth\Actions\TwoFactorAuth\DisableTwoFactorAuthentication;
use Devdojo\Auth\Actions\TwoFactorAuth\GenerateNewRecoveryCodes;
use Devdojo\Auth\Actions\TwoFactorAuth\GenerateQrCodeAndSecretKey;

name('user.two-factor-authentication');
//middleware(['auth', 'verified', 'password.confirm']); 
middleware(['auth', 'verified']);
// middleware(['auth'])

new class extends Component
{
    public $enabled = false;

    // confirmed means that it has been enabled and the user has confirmed a code
    public $confirmed = false;

    public $showRecoveryCodes = true;

    public $secret = '';
    public $codes = '';
    public $qr = '';
    
    public function mount(){
        if(is_null(auth()->user()->two_factor_confirmed_at)) {
            app(DisableTwoFactorAuthentication::class)(auth()->user());
        } else {
            $this->confirmed = true;
        }
    }

    public function enable(){

        $QrCodeAndSecret = new GenerateQrCodeAndSecretKey();
        [$this->qr, $this->secret] = $QrCodeAndSecret(auth()->user());
        
        auth()->user()->forceFill([
            'two_factor_secret' => encrypt($this->secret),
            'two_factor_recovery_codes' => encrypt(json_encode($this->generateCodes()))
        ])->save();

        $this->enabled = true;
    }

    private function generateCodes(){
        $generateCodesFor = new GenerateNewRecoveryCodes();
        return $generateCodesFor(auth()->user());
    }

    #[On('submitCode')] 
    public function submitCode($code)
    {
        if(empty($code) || strlen($code) < 6){
            // TODO - If the code is empty or it's less than 6 characters we want to show the user a message
            dd('show validation error');
            return;
        }

        //dd($this->secret);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secret, $code);

        if($valid){
            auth()->user()->forceFill([
                'two_factor_confirmed_at' => now(),
            ])->save();

            $this->confirmed = true;
        else {
            // TODO - implement an invalid message when the user enters an incorrect auth code
            dd('show invalide code message')
        }
    }

    public function disable(){
        $disable = new DisableTwoFactorAuthentication;
        $disable(auth()->user());

        $this->enabled = false;
        $this->confirmed = false;
        $this->showRecoveryCodes = true;
    }

}

?>

<x-auth::layouts.empty title="Two Factor Authentication">
    @volt('user.two-factor-authentication')
        <section class="flex justify-center items-center w-screen h-screen">

            <svg xmlns="http://www.w3.org/2000/svg" data-name="Two-Factor Authentication" viewBox="0 0 64 64"><g fill="#0a0f26"><path d="M58 2H6C3.79 2 2 3.79 2 6v38c0 2.21 1.79 4 4 4h9c.55 0 1-.45 1-1s-.45-1-1-1H6c-1.1 0-2-.9-2-2V9h34.67l3.73 2.8c.17.13.38.2.6.2h17v32c0 1.1-.9 2-2 2h-9c-.55 0-1 .45-1 1s.45 1 1 1h9c2.21 0 4-1.79 4-4V6c0-2.21-1.79-4-4-4zm-14.67 8L39.6 7.2A.984.984 0 0 0 39 7H4V6c0-1.1.9-2 2-2h52c1.1 0 2 .9 2 2v4z"/><path d="M55 8h-2c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1s-.45 1-1 1zM48 8h-2c-.55 0-1-.45-1-1s.45-1 1-1h2c.55 0 1 .45 1 1s-.45 1-1 1z"/></g><path fill="#6b71f2" d="M42 62H22c-2.21 0-4-1.79-4-4V24c0-2.21 1.79-4 4-4h20c2.21 0 4 1.79 4 4v34c0 2.21-1.79 4-4 4zM22 22c-1.1 0-2 .9-2 2v34c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V24c0-1.1-.9-2-2-2z"/><path fill="#6b71f2" d="M34 56h-4c-.55 0-1-.45-1-1s.45-1 1-1h4c.55 0 1 .45 1 1s-.45 1-1 1z"/><path fill="#0a0f26" d="M32 49c-.1 0-.19-.01-.29-.04C25.85 47.2 23 44.02 23 39.24v-6.5a1 1 0 0 1 .68-.95l8-2.74c.21-.07.44-.07.65 0l8 2.74c.4.14.68.52.68.95v6.5c0 4.78-2.85 7.96-8.71 9.72-.09.03-.19.04-.29.04zm-7-15.55v5.79c0 3.81 2.16 6.2 7 7.71 4.84-1.52 7-3.91 7-7.71v-5.79l-7-2.4z"/><path fill="#6b71f2" d="M31 42c-.26 0-.51-.1-.71-.29l-2-2a.996.996 0 1 1 1.41-1.41l1.29 1.29 3.29-3.29a.996.996 0 1 1 1.41 1.41l-4 4c-.2.2-.45.29-.71.29z"/></svg>

            <div x-data x-on:code-input-complete.window="$dispatch('submitCode', [event.detail.code])" class="flex flex-col mx-auto w-full max-w-md text-sm">

                @if($confirmed)
                    <div class="flex flex-col space-y-5">
                        <h2 class="text-xl">You have enabled two factor authentication.</h2>
                        <p>When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>    
                        @if($showRecoveryCodes)
                            <div class="relative">
                                <p class="font-bold">Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.</p>
                                <div class="grid gap-1 px-4 py-4 mt-4 max-w-xl font-mono text-sm bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-100">
                                    
                                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                        <div>{{ $code }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center">
                            <x-auth::elements.button type="primary" wire:click="regenerateCodes">Regenerate Recovery Codes</x-auto::elements.button>
                            <x-auth::elements.button type="danger" wire:click="disable">Disable</x-auto::elements.button>
                        </div>
                    </div>
                    
                @else
                    @if(!$enabled)
                        <div class="flex relative flex-col justify-start items-start space-y-5">
                            <h2 class="text-xl">You have not enabled two factor authentication.</h2>
                            <p>When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>
                            <div class="relative w-auto">
                                <x-auth::elements.button type="primary" rounded="md" size="md" wire:click="enable">Enable</x-auth>
                            </div>
                        </div>
                    @else
                        <div  class="relative space-y-5 w-full">
                            <div class="space-y-5">
                                <h2 class="text-xl">Finish enabling two factor authentication.</h2>
                                <p>When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>
                                <p class="font-bold">To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.</p>
                            </div>

                            <div class="relative mx-auto max-w-64">
                                <img src="data:image/png;base64, {{ $qr }}" style="width:400px; height:auto" />
                            </div>

                            <p class="font-semibold">
                                {{ __('Setup Key') }}: {{ $secret }}
                            </p>

                            <x-auth::elements.input-code id="auth-input-code" digits="6" eventCallback="code-input-complete" type="text" label="Code" />
                            
                            <div class="flex items-center">
                                <x-auth::elements.button type="primary" wire:click="submitCode(document.getElementById('auth-input-code').value)">Confirm</x-auto::elements.button>
                                <x-auth::elements.button type="secondary">Cancel</x-auto::elements.button>
                            </div>


                        </div>
                    @endif
                @endif
            </div>
        </section>
    @endvolt

</x-auth::layouts.empty>