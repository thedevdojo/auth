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

        $this->page = 'welcome';
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
                

                {{-- <section :class="{ '-translate-x-full' : fullscreen, }" class="flex absolute justify-center z-20 flex-shrink-0 w-[650px] items-center py-2.5 h-screen duration-300 ease-out" x-cloak>
                    @include('auth::includes.setup.options')
                </section> --}}
                <section class="relative z-10 ml-3 w-full h-screen duration-300 ease-out" x-cloak>
                    <div class="flex relative items-stretch pt-2 h-screen justify-stretch">
                        
                        <div class="flex overflow-x-scroll relative justify-center items-center w-full h-full rounded-tl-2xl border-t border-l bg-zinc-50 border-zinc-200">
                            {{-- Top and Left Gradient --}}
                            <div class="absolute top-0 left-0 z-10 w-full h-10 bg-gradient-to-b from-white to-transparent opacity-80"></div>
                            <div class="absolute top-0 left-0 z-10 w-10 h-full bg-gradient-to-r from-white to-transparent opacity-80"></div>
                            
                            <div class="flex z-20 justify-center items-start w-full h-full">
                                @include('auth::includes.setup.' . $page)
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        @endvolt
    </div>

</x-auth::layouts.setup>