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
name('auth.register');

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

        <x-auth::devdojoauth.heading text="Sign up" />

        <x-auth::devdojoauth.container>
                @volt('auth.register')
                    <form wire:submit="register" class="space-y-6">
                        <x-auth::devdojoauth.input label="Name" type="text" id="name" name="name" wire:model="name" />
                        <x-auth::devdojoauth.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                        <x-auth::devdojoauth.input label="Password" type="password" id="password" name="password" wire:model="password" />
                        <x-auth::devdojoauth.input label="Confirm Password" type="password" id="password_confirmation" name="password_confirmation" wire:model="passwordConfirmation" />
                        <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Register</x-auth::devdojoauth.button>
                    </form>
                @endvolt

                <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                    <span>Already have an account?</span>
                    <x-auth::devdojoauth.text-link href="{{ route('auth.login') }}">Sign in</x-auth::devdojoauth.text-link>
                </div>
        </x-auth::devdojoauth.container>
        
    </div>

</x-auth::layouts.app>
