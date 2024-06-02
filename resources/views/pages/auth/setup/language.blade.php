<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;

middleware(['view-auth-setup']);
name('auth.setup.language');

new class extends Component
{
    public $language;
    public $descriptions;
    private $config;

    public function mount(){
        $this->language = (object)config('devdojo.auth.language');
    }

    public function update($key, $value){
        \Config::write('devdojo.auth.language.' . $key, $value);
        Artisan::call('config:clear');

        $this->language = (object)config('devdojo.auth.language');
        $this->js('savedMessageOpen()');
    }
};

?>

<x-auth::layouts.setup>

    @volt('auth.setup.language')
        <section class="relative px-4 py-5 mx-auto w-full max-w-screen-lg">
            <x-auth::setup.full-screen-loader wire:target="update" />
            <x-auth::setup.heading title="Language" description="Update the language copy for each authenticaiton page" />
            <div class="relative w-full">
                @if(!file_exists(base_path('config/devdojo/auth/language.php')))
                    <x-auth::setup.config-notification />
                @else
                    @foreach($this->language as $parentKey => $value)
                        <div x-data="{ show: false }" class="w-full border-b border-zinc-100">
                            <div x-on:click="show=!show" :class="{ 'text-zinc-800 bg-zinc-100' : show, 'text-zinc-500 hover:text-zinc-800 hover:bg-zinc-100' : !show  }" class="flex relative justify-between items-center p-3 w-full cursor-pointer">
                                <h3>{{ ucwords(str_replace('_', ' ', Str::snake($parentKey))) }}</h3>
                                <x-phosphor-caret-down class="w-5 h-5" />
                            </div>
                            <div x-show="show" class="relative p-5 select-none" x-collapse>
                                @foreach((array)$value as $key => $value)
                                    <div class="pb-5 mb-5 border-b border-zinc-200">
                                        @if(is_bool($value))
                                            <x-auth::setup.checkbox-title-description wire:change="update('{{ $parentKey . '.' . $key }}', $el.checked)" name="{{ $key }}" :$key :title="Helper::convertSlugToTitle($key)" :checked="($value ? true : false)" />
                                        @else
                                            <x-auth::setup.input :id="$key" wire:blur="update('{{ $parentKey . '.' . $key }}', $el.value)" :label="Helper::convertSlugToTitle($key)" type="text" name="{{ $key }}" value="{{ $value }}" />
                                        @endif
                                        
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endforeach
                    
                @endif
            </div>
        </section>
    @endvolt

</x-auth::layouts.setup>
