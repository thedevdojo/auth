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

    public $showingConfirmation = false;

    public $secret = '';
    public $codes = '';
    public $qr = '';
    
    public function mount(){
        if(is_null(auth()->user()->two_factor_confirmed_at)) {
            app(DisableTwoFactorAuthentication::class)(auth()->user());
        }
    }

    public function enable(){
        $QrCodeAndSecret = new GenerateQrCodeAndSecretKey();
        [$this->qr, $this->secret] = $QrCodeAndSecret(auth()->user());
        
        auth()->user()->forceFill([
            'two_factor_secret' => encrypt($this->secret),
            'two_factor_recovery_codes' => encrypt($this->generateCodes())
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
        if(empty($code) || strlen($code) < 5){
            dd('show validation error');
            return;
        }

        //dd($this->secret);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secret, $code);

        dd($valid);
    }

}

?>

<x-auth::layouts.empty title="Two Factor Authentication">
    @volt('user.two-factor-authentication')
        <section class="flex justify-center items-center w-screen h-screen">
            <div x-data x-on:code-input-complete.window="$dispatch('submitCode', [event.detail.code])" class="flex flex-col mx-auto w-full max-w-md text-sm">

                @if($confirmed)
                    <p>Two-factor auth is current enabled and active for this user.</p>
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