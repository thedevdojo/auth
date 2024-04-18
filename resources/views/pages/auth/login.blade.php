<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

middleware(['guest']);
name('auth.login');

new class extends Component
{
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public $remember = false;

    public $authData = [];

    public function mount(){
        $this->authData = config('devdojo.auth.pages.login');
    }

    public function authenticate()
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', trans('auth.failed'));

            return;
        }

        event(new Login(auth()->guard('web'), User::where('email', $this->email)->first(), $this->remember));

        return redirect()->intended('/');
    }
};

?>

<x-auth::layouts.app>
    @volt('auth.login')
    <div class="w-full">
        <x-auth::elements.heading :text="$authData['headline'] ?? ''" />
        
        <x-auth::elements.container>
            
                
                    <form wire:submit="authenticate" class="space-y-6">
                        
                        <x-auth::elements.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                        <x-auth::elements.input label="Password" type="password" id="password" name="password" wire:model="password" />

                        <div class="flex justify-between items-center mt-6 text-sm leading-5">
                            <x-auth::elements.checkbox label="Remember me" id="remember" name="remember" wire:model="remember" />
                            <x-auth::elements.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::elements.text-link>
                        </div>

                        <x-auth::elements.button type="primary" rounded="md" submit="true">Sign in</x-auth::elements.button>
                    </form>
                
                
                <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                    <span>Don't have an account?</span>
                    <x-auth::elements.text-link href="{{ route('auth.register') }}">Sign up</x-auth::elements.text-link>
                </div>

        </x-auth::elements.container>
    </div>
    @endvolt
</x-auth::layouts.app>