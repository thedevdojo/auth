<?php

use App\Models\User;
use function Laravel\Folio\{middleware, name};
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Login;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;
use Devdojo\Auth\Traits\HasConfigs;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

if(!isset($_GET['preview']) || (isset($_GET['preview']) && $_GET['preview'] != true) || !app()->isLocal()){
    middleware(['two-factor-challenged', 'throttle:5,1']);
}
name('auth.two-factor-challenge');

new class extends Component
{
    use HasConfigs;
    
    public $recovery = false;
    public $google2fa;

    #[Validate('required|min:5')] 
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
            // TODO - this we need to autofocus the first input of the auth code input
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-auth-2fa-auth-code', {})); }, 10);");
        }
        return;
    }

    // TODO - Refactor the submitCode functionality into it's own trait so we can use this functionality here and user/two-factor-authenticaiton.blade.php

     #[On('submitCode')] 
    public function submitCode($code)
    {
        $this->auth_code = $code;

        $this->validate();

        if(empty($code) || strlen($code) < 5){
            dd('show validation error');
            return;
        }

        $user = User::find(session()->get('login.id'));

        $secret = decrypt($user->two_factor_secret);
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $code);

        if($valid){
        
            Auth::login($user);

            // clear out the session that is used to determine if the user can visit the 2fa challenge page.
            session()->forget('login.id');

            event(new Login(auth()->guard('web'), $user, true));
            return redirect()->intended('/');
        } else {
            dd('invalid');
        }

    }

    // TODO - Make sure that submitting the recovery codes work

    public function submit_recovery_code(){
        $valid = in_array($this->recovery_code, auth()->user()->two_factor_recovery_codes);

        if ($valid) {
            dd('valid yo!');
        } else {
            dd('not valid');
        }
    }
}

?>

<x-auth::layouts.app title="{{ config('devdojo.auth.language.twoFactorChallenge.page_title') }}">
    @volt('auth.two-factor-challenge')
        <x-auth::elements.container>
            <div x-data x-on:code-input-complete.window="console.log(event); $dispatch('submitCode', [event.detail.code])" class="relative w-full h-auto">
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

                <div class="mt-5 space-y-5">

                    @if(!$recovery)
                        <div class="relative">
                            <x-auth::elements.input-code wire:model="auth_code" id="auth-input-code" digits="6" eventCallback="code-input-complete" type="text" label="Code" />
                        </div>
                        @error('auth_code')
                            <p class="my-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <x-auth::elements.button rounded="md" submit="true" wire:click="submitCode(document.getElementById('auth-input-code').value)">Continue</x-auth::elements.button>
                    @else
                        <div class="relative">
                            <x-auth::elements.input label="Recovery Code" type="text" wire:model="recovery_code" id="auth-2fa-recovery-code" required />
                        </div>
                        <x-auth::elements.button rounded="md" submit="true" wire:click="submitRecoveryCode">Continue</x-auth::elements.button>
                    @endif

                    
                </div>

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
            </div>
        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>
