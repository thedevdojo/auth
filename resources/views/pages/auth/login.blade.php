<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use function Laravel\Folio\{middleware, name};
use function Livewire\Volt\{state, rules};

middleware(['guest']);
state(['email' => '', 'password' => '', 'remember' => false]);
rules(['email' => 'required|email', 'password' => 'required']);
name('auth.login');

$authenticate = function(){
    $this->validate();

    if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        $this->addError('email', trans('auth.failed'));

        return;
    }
    
    event(new Login(auth()->guard('web'), User::where('email', $this->email)->first(), $this->remember));

    return redirect()->intended('/');
}

?>

<x-auth::layouts.app>

    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <x-auth::devdojoauth.link href="/">
                <x-auth::devdojoauth.logo class="mx-auto w-auto h-10 text-gray-700 fill-current dark:text-gray-100" />
            </x-auth::devdojoauth.link>

            <h2 class="mt-5 text-2xl font-extrabold leading-9 text-center text-gray-800 dark:text-gray-200">Sign in to your account</h2>
            <div class="space-x-0.5 text-sm leading-5 text-center text-gray-600 dark:text-gray-400">
                <span class="text-pink-400">Or</span>
                <x-auth::devdojoauth.text-link href="{{ route('auth.register') }}">create a new account</x-auth::devdojoauth.text-link>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="px-10 py-0 sm:py-8 sm:shadow-sm sm:bg-white dark:sm:bg-gray-950/50 dark:border-gray-200/10 sm:border sm:rounded-lg border-gray-200/60">
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
            </div>
        </div>
        
    </div>

</x-auth::layouts.app>