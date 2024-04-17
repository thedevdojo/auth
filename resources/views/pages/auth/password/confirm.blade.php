<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('password.confirm');

new class extends Component
{
    #[Validate('required|current_password')]
    public $password = '';

    public function confirm()
    {
        $this->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended('/');
    }
};

?>

<x-auth::layouts.app>
    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <x-auth::devdojoauth.heading text="Reset password" description="Please confirm your password before continuing" />
        {{-- <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <x-auth::devdojoauth.link href="/">
                <x-auth::devdojoauth.logo class="mx-auto w-auto h-10 text-gray-700 fill-current dark:text-gray-100" />
            </x-auth::devdojoauth.link>

            <h2 class="mt-5 text-2xl font-extrabold leading-9 text-center text-gray-800 dark:text-gray-200">
                Confirm your password
            </h2>
            <p class="space-x-0.5 text-sm leading-5 text-center text-gray-600 dark:text-gray-400">
                Please confirm your password before continuing
            </p>
        </div> --}}

        <x-auth::devdojoauth.container>
                @volt('auth.password.confirm')
                    <form wire:submit="confirm" class="space-y-6">
                        <x-auth::devdojoauth.input label="Password" type="password" id="password" name="password" wire:model="password" />
                        <div class="flex justify-end items-center text-sm">
                            <x-auth::devdojoauth.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::devdojoauth.text-link>
                        </div>
                        <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Confirm password</x-auth::devdojoauth.button>
                    </form>
                @endvolt
        </x-auth::devdojoauth.container>
    </div>

</x-auth::layouts.app>