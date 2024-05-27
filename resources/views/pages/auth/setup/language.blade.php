<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;

middleware(['auth', 'view-auth-setup']);
name('auth.setup.language');

new class extends Component
{
    
};

?>

<x-auth::layouts.setup>

    @volt('auth.setup.language')
        <section class="relative px-4 py-5 mx-auto w-full max-w-screen-lg">
            <x-auth::setup.full-screen-loader wire:target="update" />
            <x-auth::setup.heading title="Language" description="Update the text copy on your login, registration, and other authentication pages" />
            <div class="relative w-full">
                @if(!file_exists(base_path('config/devdojo/auth/language.php')))
                    <x-auth::setup.config-notification />
                @else
                    <div class="mt-10">
                        addl language stuff here
                    </div>
                @endif
            </div>
        </section>
    @endvolt

</x-auth::layouts.setup>
