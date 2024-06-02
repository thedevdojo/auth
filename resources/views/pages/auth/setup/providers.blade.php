<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;
use Devdojo\ConfigWriter\ArrayFile;

middleware(['view-auth-setup']);
name('auth.setup.providers');

new class extends Component
{
    public $providers;
    public $descriptions;
    private $config;

    public function mount(){
        $this->providers = (object)config('devdojo.auth.providers');
        $this->descriptions = (object)config('devdojo.auth.descriptions');
    }

    public function update($slug, $checked){
        \Config::write('devdojo.auth.providers.' . $slug . '.active', $checked);
        Artisan::call('config:clear');
        $this->providers = (object)config('devdojo.auth.providers');
        $this->js('savedMessageOpen()');
    }
};

?>

<div>
    <x-auth::layouts.setup>

        @volt('auth.setup.providers')
            <section class="relative px-4 py-5 mx-auto w-full max-w-screen-lg">
                <x-auth::setup.full-screen-loader wire:target="update" />
                <x-auth::setup.heading title="Social Providers" description="Select the social networks that users can use for authentication" />
                <div class="relative w-full">
                    @if(!file_exists(base_path('config/devdojo/auth/providers.php')))
                        <x-auth::setup.config-notification />
                    @else
                        <div class="grid grid-cols-2">
                            @foreach($this->providers as $network_slug => $provider)

                                <div class="flex relative justify-between items-center max-w-sm border-b border-b-zinc-200">
                                    <div class="flex relative justify-start items-center py-5 space-x-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-7 h-7">
                                                @if(isset($provider['svg']) && !empty(trim($provider['svg'])))
                                                    {!! $provider['svg'] !!}
                                                @else
                                                    <span class="block w-full h-full rounded-full bg-zinc-200"></span>
                                                @endif
                                            </div>
                                            <div class="relative">
                                                <h4 class="text-base font-bold">{{ $provider['name'] }}</h4>
                                                <p class="hidden text-sm">slug: {{ $network_slug }}</p>
                                            </div>
                                        </div>
                                        <div class="relative right">
                                            @if(isset($provider['client_id']) && !empty(trim($provider['client_id'])) && isset($provider['client_secret']) && !empty(trim($provider['client_secret'])))
                                                <span x-tooltip="Keys have been added" class="flex justify-center items-center w-7 h-7 text-green-500 bg-green-100 rounded-full">
                                                    <x-phosphor-key-duotone class="w-4 h-4 text-green-500" />
                                                <span>
                                            @else
                                                <span x-tooltip="Missing keys for {{ strtoupper($network_slug) }}_CLIENT_ID and {{ strtoupper($network_slug) }}_CLIENT_SECRET inside your .env" class="flex justify-center items-center w-7 h-7 text-red-500 bg-red-100 rounded-full border-red-200">
                                                    <x-phosphor-key-duotone class="w-4 h-4 text-red-500" />
                                                <span>
                                            @endif
                                        </div>
                                        
                                    </div>
                                    <div class="flex relative items-center">
                                        @if(!isset($provider['socialite']) || !$provider['socialite'])
                                            <a href="https://devdojo.com/auth/docs/config/social-providers/#socialite-providers-package" target="_blank" class="px-2 py-0.5 text-[0.6rem] mr-1.5 text-yellow-900 bg-yellow-200 rounded-full">Requires Package</a>
                                        @endif
                                        <x-auth::setup.checkbox wire:change="update('{{ $network_slug }}', $el.checked)" :checked="($provider['active'] ? true : false)" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endvolt

    </x-auth::layouts.setup>
</div>
