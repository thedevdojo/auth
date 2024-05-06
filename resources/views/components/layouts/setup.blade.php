<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Authentication Setup</title>
        @if(config('devdojo.auth.settings.dev_mode'))
            @vite(['packages/devdojo/auth/resources/css/auth.css', 'packages/devdojo/auth/resources/css/auth.js'])
        @else
            <script src="/auth/build/assets/scripts.js"></script>
            <link rel="stylesheet" href="/auth/build/assets/styles.css" />
        @endif

        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="https://unpkg.com/tippy.js@6"></script>

        <!-- Source -->
        <script>
            document.addEventListener('alpine:init', () => {
                // Magic: $tooltip
                Alpine.magic('tooltip', el => message => {
                    let instance = tippy(el, { content: message, trigger: 'manual' })
        
                    instance.show()
        
                    setTimeout(() => {
                        instance.hide()
        
                        setTimeout(() => instance.destroy(), 150)
                    }, 2000)
                })
        
                // Directive: x-tooltip
                Alpine.directive('tooltip', (el, { expression }) => {
                    tippy(el, { content: expression })
                })
            })
        </script>
        
    </head>
<body 
    x-data="{ 
        sidebar: false,
        preview: false,
        previewMenuDropdown: false,
        previewURL: '/auth/login'
    }" 
    x-init="
        $watch('preview', function(value){
            if(value){
                console.log('new value');
                
                setTimeout(function(){
                    document.getElementById('preview').src= previewURL + '?' + Date.now();
                    setTimeout(function(){
                        document.getElementById('preview_loader').classList.add('hidden');
                        document.getElementById('preview').classList.remove('hidden');
                        document.getElementById('preview').classList.remove('opacity-0');
                    }, 500);
                    
                }, 1000);
            } else {
                setTimeout(function(){
                    document.getElementById('preview').classList.add('hidden');
                    document.getElementById('preview_loader').classList.remove('hidden');
                    document.getElementById('preview').classList.add('opacity-0');
                    document.getElementById('preview').src='about:blank';
                }, 1000);
            }
        });
    "
    class="overflow-hidden w-screen h-screen bg-gray-50 dark:bg-zinc-950">
    <div class="flex flex-col justify-start items-start w-screen h-screen">
        <div class="flex justify-center items-start w-full h-full">

            <main class="flex justify-center items-center w-full h-full">
                <div x-data="{ fullscreen: false }" class="flex relative w-full">
                    @include('auth::includes.setup.sidebar')
                    
                    <section class="relative z-10 ml-3 w-full h-screen duration-300 ease-out" x-cloak>
                        <div class="flex relative items-stretch pt-2 h-screen justify-stretch">
                            
                            <div class="flex overflow-x-hidden relative justify-center items-center w-full h-full bg-white rounded-tl-2xl border-t border-l border-zinc-200">
                                
                                <div class="flex z-20 justify-center items-start p-5 w-full h-full lg:p-0">
                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    
    <div x-data="{ open: false }" x-show="open" x-transition.opacity x-init="$watch('open', function(value){ if(value){ setTimeout(function(){ open=false }, 2000)}})" class="fixed top-0 right-0 z-50 mt-8 mr-10 text-sm text-green-500 duration-300 ease-out" @saved-message-open.window="open=true" x-cloak>Saved!</div>
    <script>
        window.savedMessageOpen = function(){
            window.dispatchEvent(new CustomEvent('saved-message-open', {}));
        }
    </script>

    
        <div x-show="preview" @click="preview=false" x-transition.opacity class="absolute inset-0 z-[98] w-screen h-screen delay-500 bg-black/50" x-cloak></div>
        <div :class="{ 'translate-y-full duration-500' : !preview, 'translate-y-0 pt-3 duration-300' : preview }"
             class="flex absolute top-0 left-0 px-3 z-[99] ease-in-out  flex-col w-screen h-screen" x-cloak>
            
            <div class="flex flex-col w-full h-full bg-white rounded-t-md">
                <div class="flex relative z-50 flex-shrink-0 justify-center items-center w-full h-10 bg-white rounded-t-md border-b border-zinc-200">
                    <div class="relative" x-on:click.outside="previewMenuDropdown=false">
                        <button x-on:click="previewMenuDropdown=!previewMenuDropdown" class="flex justify-between items-center px-3 w-64 h-7 text-xs rounded-md border cursor-pointer bg-zinc-100 hover:bg-zinc-200/70">
                            <img src="{{ url(config('devdojo.auth.appearance.favicon.light')) }}" class="w-4 h-4 -translate-x-1.5" />
                            <span class="font-medium">Login</span>
                            <x-phosphor-caret-down-fill class="ml-2 w-3 h-3" />
                        </button>
                        <div x-show="previewMenuDropdown" x-transition.scale.origin.top.opacity class="[&>button]:px-3 [&>button]:block [&>button]:rounded-md space-y-1 [&>button:hover]:bg-zinc-100 group [&>button]:text-left [&>button]:w-full [&>button]:text-sm [&>button]:py-1.5 absolute left-0 bg-white shadow-xl p-2 w-64 rounded-md top-0 mt-[33px] z-[99]">
                            <button @click="previewURL='/auth/login'">Login</button>
                            <button @click="previewURL='/auth/register'">Register</button>
                            <button @click="previewURL='/auth/verify'">Verify Account</button>
                            <button @click="previewURL='/auth/password/confirm'">Password Confirmation</button>
                            <button @click="previewURL='/auth/password/reset'">Password Reset Request</button>
                            <button @click="previewURL='/auth/password/SomeReallyLongToken'">Password Reset</button>
                        </div>
                    </div>
                    <div class="flex absolute right-0 items-center h-full">
                        <a :href="previewURL" target="_blank" class="flex flex-shrink-0 items-center px-2 py-1 mr-2 text-xs bg-white rounded-md border shadow-sm duration-200 ease-out text-zinc-500 hover:text-zinc-700 hover:border-zinc-200 group border-zinc-100">
                            <span>Preview in New Tab</span>
                            <x-phosphor-arrow-right-bold class="flex-shrink-0 ml-1 w-3 h-3 duration-200 ease-out -rotate-45 group-hover:-translate-y-px group-hover:translate-x-px" />
                        </a>
                        <button :class="{ '-translate-y-full rounded-t-md border border-zinc-200 shadow-xl' : !preview, 'rounded-tr-md border-l border-zinc-200' : preview }" x-on:click="preview=!preview" class="inline-flex z-50 items-center px-4 h-full text-sm font-medium bg-white hover:bg-zinc-100 group text-zinc-600 hover:text-zinc-800">
                            <div x-show="!preview" class="flex items-center space-x-1">
                                <x-phosphor-monitor-bold class="-ml-0.5 w-4 h-4 duration-300 ease-out translate-x-0 group-hover:-translate-x-0.5" />
                                <span>Open Preview</span>
                            </div>
                            <div x-show="preview" class="flex items-center space-x-1">
                                <x-phosphor-x-bold class="-ml-0.5 w-3.5 h-3.5 duration-300 ease-out" />
                            </div>
                        </button>
                    </div>
                </div>
                <div id="preview_loader" class="absolute top-0 left-0 z-40 justify-center items-center px-3 mt-10 w-full h-full">
                    <div class="flex justify-center items-center w-full h-full bg-zinc-100">
                        <svg class="w-5 h-5 animate-spin text-zinc-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <iframe id="preview" :src="previewURL + '?preview=true'" class="hidden overflow-hidden relative z-30 w-full h-full opacity-0 duration-300 ease-out"></iframe>
            </div>
        </div>
        

</body>
</html>