<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

middleware(['auth', 'view-auth-setup']);
name('auth.setup');

new class extends Component
{

};

?>

<x-auth::layouts.setup>

        @volt('auth.setup')
            <section class="px-4 py-14 mx-auto max-w-screen-lg">
                @if(!file_exists(base_path('config/devdojo/auth/settings.php')))
                    <x-auth::setup.config-notification />
                @endif
                <div class="mb-10">
                    <h2 class="mb-2 text-2xl font-bold text-left">Authentication Setup</h2>
                    <p class="text-sm text-left text-gray-600">Welcome to your authentication setup. Below you will find sections to help you configure and customize the auth in your application.</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <x-auth::setup.welcome-card link="auth/setup/appearance" icon="appearance" title="Change The Appearance" description="Change the appearance of your auth screens, add a logo, modify the color, and more."></x-auth::setup.welcome-card>
                    <x-auth::setup.welcome-card link="auth/setup/providers" icon="social-providers" title="Add/Edit Social Providers" description="Select the social networks that users can use for authentication."></x-auth::setup.welcome-card>
                    <x-auth::setup.welcome-card link="auth/setup/language" icon="language" title="Update Language Copy" description="Update the text copy on your login, registration, and other authentication pages"></x-auth::setup.welcome-card>
                    <x-auth::setup.welcome-card link="auth/setup/settings" icon="settings" title="Modify Settings" description="Adjust specific authentication features and enable/disable functionality."></x-auth::setup.welcome-card>
                </div>
                <div @click="preview=true" class="flex items-center px-7 py-5 mt-6 space-x-5 w-full h-auto bg-white rounded-md border duration-300 ease-out cursor-pointer hover:bg-zinc-50 border-zinc-200">
                    <span class="block flex-shrink-0 w-24 h-24">
                        @include('auth::includes.setup.icons.preview')
                    </span>
                    <div class="relative">
                        <p class="text-lg font-semibold text-zinc-800">Preview Your Authentication Pages</p>
                        <p class="text-sm underline">Click here to see what your authentication pages look like.</p>
                    </div>
                </div>
                <div class="relative px-5 py-4 mt-6 w-full text-gray-900 bg-gray-100 rounded-md border border-gray-200 dark:bg-zinc-700 dark:text-gray-300 dark:border-gray-700">
                    <div class="text-sm opacity-80">To learn more about this authentication package, be sure to <a href="https://auth.devdojo.com/docs" target="_blank" class="underline">visit the documentation</a> or <a href="https://github.com/thedevdojo/auth" target="_blank" class="underline">view the project on Github</a>.</div>
                </div>
            </section>

        @endvolt

</x-auth::layouts.setup>