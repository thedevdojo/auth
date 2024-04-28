<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;

name('auth.setup.settings');

new class extends Component
{
    public $settings;
    public $descriptions;

    public function mount(){
        $this->settings = (object)config('devdojo.auth.settings');
        $this->descriptions = (object)config('devdojo.auth.descriptions');
    }
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
                @foreach((array)$this->settings as $key => $value)
                    <div class="pb-5 mb-5 border-b border-zinc-200">
                        @if(is_bool($value))
                            <div class="flex relative items-start">
                                <div class="pr-2 translate-y-[3px]">
                                    <x-auth::setup.checkbox name="{{ $key }}" :checked="($value ? true : false)" />
                                </div>
                        @endif
                        <div class="relative">
                            <label for="{{ $key }}" class="block text-sm font-medium leading-6 text-gray-900">{{ Helper::convertSlugToTitle($key) }}</label>
                            @if(($this->descriptions->settings[$key]))
                                <p class="text-sm leading-6 text-gray-400">{{ $this->descriptions->settings[$key] }}</p>
                            @endif
                        </div>
                        @if(is_bool($value))
                            </div>
                        @else
                            <div class="max-w-sm">
                                <x-auth::setup.input type="text" name="{{ $key }}" value="{{ $value }}" />
                            </div>
                        @endif
                        
                    </div>
                @endforeach
        </section>
    @endvolt

</x-auth::layouts.setup>
