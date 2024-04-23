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

    @volt('auth.password.confirm')
        <x-auth::elements.container>
            <x-auth::elements.heading text="Confirm password" description="Please confirm your password before continuing" />        
            <form wire:submit="confirm" class="mt-5 space-y-5">
                <x-auth::elements.input label="Password" type="password" id="password" name="password" wire:model="password" />
                <x-auth::elements.button type="primary" rounded="md" submit="true">Confirm password</x-auth::elements.button>
            </form>
        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>