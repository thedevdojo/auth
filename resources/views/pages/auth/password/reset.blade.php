<?php

use Illuminate\Support\Facades\Password;
use function Laravel\Folio\name;
use function Livewire\Volt\{state, rules};

state(['email' => null, 'emailSentMessage' => false]);
rules(['email' => 'required|email']);
name('auth.password.request');



$sendResetPasswordLink = function(){
    $this->validate();

    $response = Password::broker()->sendResetLink(['email' => $this->email]);

    if ($response == Password::RESET_LINK_SENT) {
        $this->emailSentMessage = trans($response);

        return;
    }

    $this->addError('email', trans($response));
}

?>

<x-auth::layouts.app>

    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <x-auth::devdojoauth.link href="/">
                <x-auth::devdojoauth.logo class="mx-auto w-auto h-10 text-gray-700 fill-current dark:text-gray-100" />
            </x-auth::devdojoauth.link>

            <h2 class="mt-5 text-2xl font-extrabold leading-9 text-center text-gray-800 dark:text-gray-200">
                Reset password
            </h2>
            <div class="space-x-0.5 text-sm leading-5 text-center text-gray-600 dark:text-gray-400">
                <span>Or</span>
                <x-auth::devdojoauth.text-link href="{{ route('auth.login') }}">return to login</x-auth::devdojoauth.text-link>
            </div>
        </div>

        @volt('auth.password.reset')
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="px-10 py-0 sm:py-8 sm:shadow-sm sm:bg-white dark:sm:bg-gray-950/50 dark:border-gray-200/10 sm:border sm:rounded-lg border-gray-200/60">
                    @if ($emailSentMessage)
                        <div class="p-4 bg-green-50 rounded-md dark:bg-green-600">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-400 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>

                                <div class="ml-3">
                                    <p class="text-sm font-medium leading-5 text-green-800 dark:text-green-200">
                                        {{ $emailSentMessage }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <form wire:submit="sendResetPasswordLink" class="space-y-6">
                            <x-auth::devdojoauth.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                            <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Send password reset link</x-auth::devdojoauth.button>
                        </form>
                    @endif
                </div>
            </div>
        @endvolt
        
    </div>

</x-auth::layouts.app>