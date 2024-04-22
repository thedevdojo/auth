<?php

use Illuminate\Support\Facades\Password;
use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('auth.password.request');

new class extends Component
{
    #[Validate('required|email')]
    public $email = null;

    public $emailSentMessage = false;

    public function sendResetPasswordLink()
    {
        $this->validate();

        $response = Password::broker()->sendResetLink(['email' => $this->email]);

        if ($response == Password::RESET_LINK_SENT) {
            $this->emailSentMessage = trans($response);

            return;
        }

        $this->addError('email', trans($response));
    }
};

?>

<x-auth::layouts.app>


    @volt('auth.password.reset')
        <x-auth::elements.container>

            <x-auth::elements.heading text="Reset password" />
            
            @if ($emailSentMessage)
                <div class="p-4 mt-5 bg-green-50 rounded-md dark:bg-green-600">
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
                <form wire:submit="sendResetPasswordLink" class="mt-5 space-y-3">
                    <x-auth::elements.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                    <x-auth::elements.button type="primary" rounded="md" submit="true">Send password reset link</x-auth::elements.button>
                </form>
            @endif
            <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                <span>Or</span>
                <x-auth::elements.text-link href="{{ route('auth.login') }}">return to login</x-auth::elements.text-link>
            </div>
        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>