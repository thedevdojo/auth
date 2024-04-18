<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Authentication Setup</title>
        @if(config('devdojo.auth.auth.dev'))
            @vite(['packages/devdojo/auth/resources/css/auth.css', 'packages/devdojo/auth/resources/css/auth.js'])
        @else
            <script src="/auth/build/assets/scripts.js"></script>
            <link rel="stylesheet" href="/auth/build/assets/styles.css" />
        @endif
        
    </head>
<body class="bg-gray-50 dark:bg-zinc-950">
    <div class="flex justify-center items-start w-screen h-screen">
        
        <div class="w-80 h-screen">
            <div class="flex items-center px-5 space-x-1.5 w-full h-20">
                <x-auth::elements.logo class="w-auto h-7"></x-auth::elements.log>
                <h1 class="text-base font-bold leading-none">Authentication</h1>
                <p class="flex text-sm font-light leading-none">setup</p>
            </div>
            <div class="px-5 py-2">
                <a href="https://auth.devdojo.com/docs" target="_blank" class="block p-5 text-xs bg-white rounded-xl border duration-300 ease-out hover:shadow-md opacity-[0.98] hover:opacity-100 border-zinc-200">
                    <span class="flex flex-col">
                        <span class="font-semibold">Learn about configs & setup</span>
                        <span class="underline">Visit the documenation here</span>
                    </span>
                </a>
            </div>
            <x-auth::setup.sidebar-links></x-auth::setup.sidebar-links>
        </div>
        <div class="flex items-stretch pt-2 w-full h-screen justify-stretch">
            <div class="w-full h-full bg-white rounded-tl-2xl border-t border-l border-zinc-200">
                {{ $slot }}
            </div>
        </div>
    </div>
    
</body>
</html>