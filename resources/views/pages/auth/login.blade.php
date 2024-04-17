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

    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <x-auth::devdojoauth.heading text="Sign in" />

        <x-auth::devdojoauth.container>
            
                @volt('auth.login')
                    <form wire:submit="authenticate" class="space-y-6">
                        
                        <x-auth::devdojoauth.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                        <x-auth::devdojoauth.input label="Password" type="password" id="password" name="password" wire:model="password" />

                        <div class="flex justify-between items-center mt-6 text-sm leading-5">
                            <x-auth::devdojoauth.checkbox label="Remember me" id="remember" name="remember" wire:model="remember" />
                            <x-auth::devdojoauth.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::devdojoauth.text-link>
                        </div>

                        <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Sign in</x-auth::devdojoauth.button>
                    </form>
                @endvolt
                
                <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                    <span>Don't have an account?</span>
                    <x-auth::devdojoauth.text-link href="{{ route('auth.register') }}">Sign up</x-auth::devdojoauth.text-link>
                </div>

        </x-auth::devdojoauth.container>
        
    </div>

</x-auth::layouts.app>