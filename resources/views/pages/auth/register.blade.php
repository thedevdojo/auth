<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

use function Laravel\Folio\{middleware, name};
use function Livewire\Volt\{state, rules};

middleware(['guest']);
state(['name' => '', 'email' => '', 'password' => '', 'passwordConfirmation' => '']);
rules(['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required|min:8|same:passwordConfirmation']);
name('register');

$register = function(){
    $this->validate();

    $user = User::create([
        'email' => $this->email,
        'name' => $this->name,
        'password' => Hash::make($this->password),
    ]);

    event(new Registered($user));

    Auth::login($user, true);

    return redirect()->intended('/');
}

?>

<x-auth::layouts.app>

    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <x-auth::devdojoauth.link href="{{ route('home') }}">
                <x-auth::devdojoauth.logo class="mx-auto w-auto h-10 text-gray-700 fill-current dark:text-gray-100" />
            </x-auth::devdojoauth.link>
            <h2 class="mt-5 text-2xl font-extrabold leading-9 text-center text-gray-800 dark:text-gray-200">Create a new account</h2>
            <div class="space-x-0.5 text-sm leading-5 text-center text-gray-600 dark:text-gray-400">
                <span>Or</span>
                <x-auth::devdojoauth.text-link href="{{ route('login') }}">sign in to your account</x-auth::devdojoauth.text-link>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="px-10 py-0 sm:py-8 sm:shadow-sm sm:bg-white dark:sm:bg-gray-950/50 dark:border-gray-200/10 sm:border sm:rounded-lg border-gray-200/60">
                @volt('auth.register')
                    <form wire:submit="register" class="space-y-6">
                        <x-auth::devdojoauth.input label="Name" type="text" id="name" name="name" wire:model="name" />
                        <x-auth::devdojoauth.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                        <x-auth::devdojoauth.input label="Password" type="password" id="password" name="password" wire:model="password" />
                        <x-auth::devdojoauth.input label="Confirm Password" type="password" id="password_confirmation" name="password_confirmation" wire:model="passwordConfirmation" />
                        <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Register</x-auth::devdojoauth.button>
                    </form>
                @endvolt
            </div>
        </div>
        
    </div>

</x-auth::layouts.app>
