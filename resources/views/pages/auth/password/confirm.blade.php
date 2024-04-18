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

    <x-auth::elements.heading text="Reset password" description="Please confirm your password before continuing" />

    <x-auth::elements.container>
            @volt('auth.password.confirm')
                <form wire:submit="confirm" class="space-y-6">
                    <x-auth::elements.input label="Password" type="password" id="password" name="password" wire:model="password" />
                    <div class="flex justify-end items-center text-sm">
                        <x-auth::elements.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::elements.text-link>
                    </div>
                    <x-auth::elements.button type="primary" rounded="md" submit="true">Confirm password</x-auth::elements.button>
                </form>
            @endvolt
    </x-auth::elements.container>

</x-auth::layouts.app>