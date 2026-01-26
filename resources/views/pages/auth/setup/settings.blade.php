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

        $prefixConfig = [
            'registration' => ['title' => 'Registration', 'description' => 'User registration settings'],
            'password' => ['title' => 'Password Security', 'description' => 'Password strength and validation', 'collapsed' => true],
            'login' => ['title' => 'Login', 'description' => 'Login behavior settings'],
            'social' => ['title' => 'Social Providers', 'description' => 'Social login settings'],
            'redirect' => ['title' => 'Redirects', 'description' => 'Where to send users after actions'],
        ];

        return collect($settings)
            ->mapToGroups(fn ($value, $key) => [
                (collect($prefixConfig)->keys()->first(fn ($p) => str_starts_with($key, $p.'_')) ?? 'general') => [$key => $value]
            ])
            ->map(fn ($items, $group) => array_merge(
                $prefixConfig[$group] ?? ['title' => 'General', 'description' => 'Basic authentication settings'],
                ['settings' => $items->collapse()->all()]
            ))
            ->sortBy(fn ($g, $k) => [$k !== 'general', $g['collapsed'] ?? false, $k])
            ->all();
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
