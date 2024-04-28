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
        <section class="px-4 py-14 mx-auto w-full max-w-screen-lg">
            <div class="mb-10">
                <h2 class="mb-2 text-2xl font-bold text-left">Settings</h2>
                <p class="text-sm text-left text-gray-600">Adjust specific authentication features and enable/disable functionality.</p>
            </div>
            <p>Settings updates here</p>
        </section>
    @endvolt

</x-auth::layouts.setup>
