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

    public $page;

    public function mount(){
        $this->authData = config('devdojo.auth.pages.login');
        $this->heading = $this->authData['heading'];

        $this->page = 'login';
    }

    public function setPage($page){
        $this->page = $page;
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
            <div x-data="{ fullscreen: false }" class="flex relative w-full">
                @include('auth::includes.setup.sidebar')
                <x-auth::setup.top-button
                    icon="arrow-left"
                    class="fixed top-0 left-0 z-30 mt-5 ml-5"
                    @click="sidebar=!sidebar" 
                    ::class="{ 'translate-x-0.5' : fullscreen }"
                    x-cloak
                >Menu</x-auth::setup.top-button>

                <section :class="{ '-translate-x-full' : fullscreen, }" class="flex absolute justify-center z-20 flex-shrink-0 w-[650px] items-center py-2.5 h-screen duration-300 ease-out" x-cloak>
                    @include('auth::includes.setup.options')
                </section>
                <section :class="{ 'pl-[640px]' : !fullscreen, 'pl-0' : fullscreen }" class="relative z-10 ml-3 w-full h-screen duration-300 ease-out" x-cloak>
                    <div class="flex relative items-stretch pt-2 h-screen justify-stretch">
                        <x-auth::setup.top-button
                            icon="corners-out"
                            class="absolute top-0 right-0 z-50 mt-5 mr-2"
                            @click="fullscreen=!fullscreen"
                        >Fullscreen</x-auth::setup.top-button>
                        <div class="flex overflow-hidden relative justify-center items-center w-full h-full rounded-tl-2xl border-t border-l bg-zinc-50 border-zinc-200">
                            {{-- Top and Left Gradient --}}
                            <div class="absolute top-0 left-0 z-10 w-full h-10 bg-gradient-to-b from-white to-transparent opacity-80"></div>
                            <div class="absolute top-0 left-0 z-10 w-10 h-full bg-gradient-to-r from-white to-transparent opacity-80"></div>
                            
                            <div class="flex z-20 justify-center items-center w-full h-full">
                                Login Page Here
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        @endvolt
    </div>

</x-auth::layouts.setup>