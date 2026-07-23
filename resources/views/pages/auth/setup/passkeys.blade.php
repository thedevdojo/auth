<?php

use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new
#[Layout('auth::components.layouts.setup')]
class extends Component {
    public $settings;

    public $descriptions;

    public function mount()
    {
        $this->settings = (object)config('devdojo.auth.settings');
        $this->descriptions = (object)config('devdojo.auth.descriptions');
    }

    public function update($key, $value)
    {
        \Config::write('devdojo.auth.settings.' . $key, $value);
        Artisan::call('config:clear');

        $this->settings = (object)config('devdojo.auth.settings');
        $this->js('savedMessageOpen()');
    }
};

?>

<section class="relative px-4 py-5 mx-auto w-full max-w-(--breakpoint-lg)">
    <x-auth::setup.full-screen-loader wire:target="update"/>
    <x-auth::setup.heading title="Passkeys"
                           description="Enable passwordless sign-in with passkeys on your authentication screens."/>
    <div class="relative w-full">
        @if(!file_exists(base_path('config/devdojo/auth/settings.php')))
            <x-auth::setup.config-notification/>
        @else
            <div class="p-5 mb-6 text-sm border rounded-lg border-zinc-200 bg-zinc-50">
                <p class="font-medium text-zinc-900">Before enabling passkeys</p>
                <ul class="mt-2 space-y-1 list-disc list-inside text-zinc-600">
                    <li>Publish the passkeys config: <code class="text-xs">php artisan vendor:publish
                            --tag=auth:passkeys-config</code></li>
                    <li>Run the passkeys migration: <code class="text-xs">php artisan vendor:publish
                            --tag=passkeys-migrations --provider="Laravel\Passkeys\PasskeysServiceProvider"</code></li>
                    <li>Ensure your user model uses <code class="text-xs">PasskeyAuthenticatable</code></li>
                </ul>
            </div>

            <div class="flex relative justify-between items-center max-w-sm border-b border-b-zinc-200">
                <div class="flex relative justify-start items-center py-5 space-x-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 text-zinc-700">
                            <x-phosphor-fingerprint class="w-full h-full"/>
                        </div>
                        <div class="relative">
                            <h4 class="text-base font-bold">Passkey Sign-In</h4>
                            <p class="text-sm text-zinc-500">Show passkey buttons on login screens</p>
                        </div>
                    </div>
                </div>
                <div class="flex relative items-center">
                    <x-auth::setup.checkbox wire:change="update('enable_passkeys', $event.target.checked)"
                                            :checked="($settings->enable_passkeys ? true : false)"/>
                </div>
            </div>
        @endif
    </div>
</section>
    