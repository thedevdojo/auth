<?php

use Livewire\Volt\Component;
use Devdojo\Auth\Helper;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

middleware(['view-auth-setup']);
name('auth.setup.settings');

new class extends Component
{
    public $settings;

    public $descriptions;

    private $config;

    public function mount()
    {
        $this->settings = (object) config('devdojo.auth.settings');
        $this->descriptions = (object) config('devdojo.auth.descriptions');
    }

    public function update($key, $value)
    {
        \Config::write('devdojo.auth.settings.'.$key, $value);
        Artisan::call('config:clear');

        $this->settings = (object) config('devdojo.auth.settings');
        $this->js('savedMessageOpen()');
    }

    public function getGroupedSettings()
    {
        $settings = (array) $this->settings;
        $groups = [
            'general' => [
                'title' => 'General',
                'description' => 'Basic authentication settings',
                'keys' => ['redirect_after_auth', 'redirect_after_logout', 'enable_branding', 'dev_mode', 'include_wire_navigate'],
            ],
            'registration' => [
                'title' => 'Registration',
                'description' => 'User registration settings',
                'keys' => ['registration_enabled', 'registration_show_password_same_screen', 'registration_include_name_field', 'registration_include_password_confirmation_field', 'registration_require_email_verification', 'enable_email_registration'],
            ],
            'password' => [
                'title' => 'Password Security',
                'description' => 'Password strength and validation requirements',
                'keys' => ['password_min_length', 'password_require_uppercase', 'password_require_numeric', 'password_require_special_character', 'password_require_uncompromised', 'password_show_requirements'],
                'collapsed' => true,
            ],
            'login' => [
                'title' => 'Login & Social',
                'description' => 'Login behavior and social provider settings',
                'keys' => ['login_show_social_providers', 'social_providers_location', 'center_align_social_provider_button_content', 'center_align_text', 'check_account_exists_before_login'],
            ],
            'two_factor' => [
                'title' => 'Two-Factor Authentication',
                'description' => '2FA settings',
                'keys' => ['enable_2fa'],
            ],
        ];

        $grouped = [];
        $usedKeys = [];

        foreach ($groups as $groupKey => $group) {
            $grouped[$groupKey] = $group;
            $grouped[$groupKey]['settings'] = [];
            foreach ($group['keys'] as $key) {
                if (isset($settings[$key])) {
                    $grouped[$groupKey]['settings'][$key] = $settings[$key];
                    $usedKeys[] = $key;
                }
            }
        }

        // Add any ungrouped settings to 'other'
        $ungrouped = array_diff_key($settings, array_flip($usedKeys));
        if (! empty($ungrouped)) {
            $grouped['other'] = [
                'title' => 'Other',
                'description' => 'Additional settings',
                'settings' => $ungrouped,
            ];
        }

        return $grouped;
    }
};

?>

<x-auth::layouts.setup>

    @volt('auth.setup.settings')
        <section class="relative px-4 py-5 mx-auto w-full max-w-(--breakpoint-lg)">
            <x-auth::setup.full-screen-loader wire:target="update" />
            <x-auth::setup.heading title="Settings" description="Adjust specific authentication features and enable/disable functionality." />
            <div class="relative w-full">
                @if(!file_exists(base_path('config/devdojo/auth/settings.php')))
                    <x-auth::setup.config-notification />
                @else
                    <div class="mt-10">
                        @foreach($this->getGroupedSettings() as $groupKey => $group)
                            <div x-data="{ open: {{ ($group['collapsed'] ?? false) ? 'false' : 'true' }} }" class="mb-6 border border-zinc-200 rounded-lg overflow-hidden">
                                <button 
                                    type="button" 
                                    @click="open = !open" 
                                    class="flex items-center justify-between w-full px-4 py-3 text-left bg-zinc-50 hover:bg-zinc-100 transition-colors"
                                >
                                    <div>
                                        <h3 class="text-sm font-semibold text-zinc-900">{{ $group['title'] }}</h3>
                                        <p class="text-xs text-zinc-500">{{ $group['description'] }}</p>
                                    </div>
                                    <svg 
                                        class="w-5 h-5 text-zinc-400 transition-transform duration-200" 
                                        :class="{ 'rotate-180': open }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-collapse class="px-4 py-4 space-y-4 bg-white">
                                    @foreach($group['settings'] as $key => $value)
                                        <div class="pb-4 border-b border-zinc-100 last:border-b-0 last:pb-0">
                                            @php
                                                $description = ($this->descriptions->settings[$key] ?? '');
                                            @endphp
                                            @if(is_bool($value))
                                                <x-auth::setup.checkbox-title-description wire:change="update('{{ $key }}', $event.target.checked)" name="{{ $key }}" :$key :title="Helper::convertSlugToTitle($key)" :$description :checked="($value ? true : false)" />
                                            @else
                                                <x-auth::setup.input :id="$key" wire:blur="update('{{ $key }}', $event.target.value)" :$description :label="Helper::convertSlugToTitle($key)" type="text" name="{{ $key }}" value="{{ $value }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endvolt

</x-auth::layouts.setup>
