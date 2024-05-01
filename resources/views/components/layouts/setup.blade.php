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
<body x-data="{ sidebar: false }" class="bg-gray-50 dark:bg-zinc-950">
    <div class="flex flex-col justify-start items-start w-screen h-screen">
        <div class="flex justify-center items-start w-full h-full">

            <main class="flex justify-center items-center w-full h-full">
                <div x-data="{ fullscreen: false }" class="flex relative w-full">
                    @include('auth::includes.setup.sidebar')
                    
                    <section class="relative z-10 ml-3 w-full h-screen duration-300 ease-out" x-cloak>
                        <div class="flex relative items-stretch pt-2 h-screen justify-stretch">
                            
                            <div class="flex overflow-x-scroll relative justify-center items-center w-full h-full bg-white rounded-tl-2xl border-t border-l border-zinc-200">
                                
                                <div class="flex z-20 justify-center items-start w-full h-full">
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

</body>
</html>