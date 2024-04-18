<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('auth.setup');

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

<x-auth::layouts.setup>

    <div class="flex justify-center items-start w-full h-full">
        @volt('auth.setup')
            <x-auth::elements.container>
                test
            </x-auto::elements.container>
            {{-- <iframe src="/auth/login" class="w-full h-full"></iframe> --}}
        @endvolt
        
    </div>

</x-auth::layouts.setup>