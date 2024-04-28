<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('auth.setup.settings');

new class extends Component
{

};

?>

<x-auth::layouts.setup>

    @volt('auth.setup.settings')
        <section class="px-4 py-5 mx-auto w-full max-w-screen-lg">
            <div class="mb-10">
                <a href="/auth/setup" class="inline-flex items-center px-4 py-1.5 mb-3 space-x-1 text-sm font-medium rounded-full group bg-zinc-100 text-zinc-600 hover:text-zinc-800">
                    <x-phosphor-arrow-left-bold class="w-3 h-3 duration-300 ease-out translate-x-0 group-hover:-translate-x-0.5" />
                    <span>Back</span>
                </a>
                <h2 class="mb-2 text-2xl font-bold text-left">Settings</h2>
                <p class="text-sm text-left text-gray-600">Adjust specific authentication features and enable/disable functionality.</p>
            </div>
            <p>Settings updates here</p>
        </section>
    @endvolt

</x-auth::layouts.setup>
