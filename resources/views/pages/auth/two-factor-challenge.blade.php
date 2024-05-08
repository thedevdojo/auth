<?php

use App\Models\User;
use function Laravel\Folio\{middleware, name};
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;
use Devdojo\Auth\Traits\HasConfigs;
use Illuminate\Support\Facades\Cache;

//middleware(['guest']);
name('auth.two-factor-challenge');

new class extends Component
{
    use HasConfigs;
    
    public $recovery = false;
    public $google2fa;

    public $auth_code;
    public $recovery_code;

    public function mount()
    {
        $this->loadConfigs();
        $this->recovery = false;
    }

    public function switchToRecovery()
    {
        $this->recovery = !$this->recovery;
        if($this->recovery){
            $this->js("setTimeout(function(){ console.log('made'); window.dispatchEvent(new CustomEvent('focus-auth-2fa-recovery-code', {})); }, 10);");
        } else {
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-auth-2fa-auth-code', {})); }, 10);");
        }
        return;
    }

    public function submit_auth_code()
    {
        $google2fa = new Google2FA();
        //$this->verify(auth()->user()->two_factor_secret, $this->auth_code, $google2fa);
        $valid = $google2fa->verifyKey(decrypt(auth()->user()->two_factor_secret), $this->auth_code);

        if ($valid) {
            dd('Valid!');
        } else {
            dd('Failed');
        }
    }

    public function submit_recovery_code(){
        $valid = in_array($this->recovery_code, auth()->user()->two_factor_recovery_codes);

        if ($valid) {
            dd('valid yo!');
        } else {
            dd('not valid');
        }
    }

    /*private function verify($secret, $code, $google2fa)
    {
        $cachedTimestampKey = 'auth.2fa_codes.'.md5($code);

        if (is_int($customWindow = config('fortify-options.two-factor-authentication.window'))) {
            $google2fa->setWindow($customWindow);
        }

        $timestamp = $google2fa->verifyKeyNewer(
            $secret, $code, Cache::get($cachedTimestampKey)
        );

        if ($timestamp !== false) {
            if ($timestamp === true) {
                $timestamp = $google2fa->getTimestamp();
            }

            optional($cache)->put($cachedTimestampKey, $timestamp, ($google2fa->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }*/
}

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.twoFactorChallenge.page_title') }}">
    @volt('auth.twofactorchallenge')
        <x-auth::elements.container>
        
            @if(!$recovery)
                <x-auth::elements.heading 
                    :text="($language->twoFactorChallenge->headline_auth ?? 'No Heading')"
                    :description="($language->twoFactorChallenge->subheadline_auth ?? 'No Description')"
                    :show_subheadline="($language->twoFactorChallenge->show_subheadline_auth ?? false)" />
            @else
                <x-auth::elements.heading 
                    :text="($language->twoFactorChallenge->headline_recovery ?? 'No Heading')"
                    :description="($language->twoFactorChallenge->subheadline_recovery ?? 'No Description')"
                    :show_subheadline="($language->twoFactorChallenge->show_subheadline_recovery ?? false)" />
            @endif

            <form wire:submit="submit_auth_code" class="mt-5 space-y-5">

                @if(!$recovery)
                    <div class="relative">
                        <x-auth::elements.input label="Code" type="text" wire:model="auth_code" autofocus="true" id="auth-2fa-auth-code" required />
                    </div>
                @else
                    <div class="relative">
                        <x-auth::elements.input label="Recovery Code" type="text" wire:model="recovery_code" id="auth-2fa-recovery-code" required />
                    </div>
                @endif

                <x-auth::elements.button rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>

            <div class="mt-5 space-x-0.5 text-sm leading-5 text-left" style="color:{{ config('devdojo.auth.appearance.color.text') }}">
                <span class="opacity-[47%]">or you can </span>
                <span class="font-medium underline opacity-60 cursor-pointer" wire:click="switchToRecovery" href="#_">
                    @if(!$recovery)
                        <span>login using a recovery code</span>
                    @else
                        <span>login using an authentication code</span>
                    @endif
                </span>
            </div>
        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>
