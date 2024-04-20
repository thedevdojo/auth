<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('auth.setup');

new class extends Component
{
    #[Validate('required|current_password')]
    public $authData = '';

    public $heading;

    public function mount(){
        $this->authData = config('devdojo.auth.pages.login');
        $this->heading = $this->authData['heading'];
    }

    public function confirm()
    {
        $this->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended('/');
    }

};

?>

<x-auth::layouts.setup>

    <div class="flex justify-center items-center w-full h-full">
        @volt('auth.setup')
            <div x-data="{ fullscreen: false }" class="flex relative space-x-3 w-full">
                <div @click="sidebar=!sidebar" :class="{ 'translate-x-1' : fullscreen }"  class="flex fixed top-0 left-0 z-20 items-center px-3 py-2 mt-5 ml-5 text-xs font-medium bg-white rounded-lg border duration-300 ease-out cursor-pointer hover:bg-zinc-200 border-zinc-200">
                            <x-phosphor-arrow-left class="mr-1 w-3 h-3" />
                            <span>Menu</span>
                        </div>

                <section :class="{ '-translate-x-full' : fullscreen, }" class="flex absolute justify-center flex-shrink-0 w-[650px] items-center py-2.5 h-screen duration-300 ease-out">
                    <div class="flex flex-col justify-center items-center w-full max-w-md h-full">
                        
                        <div class="px-5 w-full">
                            <h2 class="mb-2 text-lg font-semibold">Let's configure your login page</h2>
                            <p class="mb-6 text-sm text-gray-700">These configurations can be changed anytime in the authentication setup page.</p>
                        </div>
                        <div class="w-full">
                            <div class="p-[3px] w-full rounded-xl bg-zinc-200/60">
                                <div class="overflow-hidden mx-auto bg-white rounded-xl border border-zinc-300">
                                    <div class="p-5 border-b border-zinc-200">
                                        <x-auth::setup.input type="text" label="Heading Text" x-on:keyup="document.getElementById('auth-heading').innerText = $el.value" wire:model="authData.heading" />
                                    </div>
                                    <div class="relative divide-y divide-zinc-200">
                                        <div class="px-5 py-3 text-sm font-semibold text-gray-700">Sign in options</div>
                                        <x-auth::setup.checkbox-row icon="envelope" text="Email" />
                                        <x-auth::setup.checkbox-row icon="phone" text="Phone Number" />
                                        <x-auth::setup.checkbox-row icon="user" text="Username" />
                                        <x-auth::setup.checkbox-row icon="google-logo" text="Google" />
                                        <x-auth::setup.checkbox-row icon="facebook-logo" text="Facebook" />
                                        <x-auth::setup.checkbox-row icon="apple-logo" text="Apple" />
                                        <x-auth::setup.checkbox-row icon="github-logo" text="Github" />
                                    </div>
                                </div>
                                <div class="flex justify-between items-center px-3 py-3 text-sm">
                                    <button class="px-4 py-2 w-auto font-medium rounded-lg bg-zinc-300/40 text-zinc-600">Reset to Default</button>
                                    <x-auth::setup.button>Save</x-auth::setup.button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section :class="{ 'pl-[640px]' : !fullscreen, 'pl-0' : fullscreen }" class="relative z-10 w-full h-screen duration-300 ease-out">
                    <div class="flex relative items-stretch pt-2 h-screen justify-stretch">
                        <div @click="fullscreen=!fullscreen"  class="flex absolute top-0 right-0 z-50 items-center px-3 py-2 mt-5 mr-2 text-xs font-medium bg-white rounded-lg border cursor-pointer hover:bg-zinc-200 border-zinc-200">
                                <x-phosphor-arrows-out class="mr-1 w-3 h-3" />
                                <span>Fullscreen</span>
                            </div>
                        <div class="flex overflow-hidden relative justify-center items-center w-full h-full rounded-tl-2xl border-t border-l bg-zinc-50 border-zinc-200">
                            
                            {{-- Top and Left Gradient --}}
                            <div class="absolute top-0 left-0 z-10 w-full h-10 bg-gradient-to-b from-white to-transparent opacity-80"></div>
                            <div class="absolute top-0 left-0 z-10 w-10 h-full bg-gradient-to-r from-white to-transparent opacity-80"></div>
                            
                            <div class="flex z-20 justify-center items-center w-full h-full">
                                @include('auth::includes.login')
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        @endvolt
    </div>

</x-auth::layouts.setup>