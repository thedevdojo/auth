<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @if(config('devdojo.auth.settings.dev'))
            @vite(['packages/devdojo/auth/resources/css/auth.css', 'packages/devdojo/auth/resources/css/auth.js'])
        @else
            <script src="/auth/build/assets/scripts.js"></script>
            <link rel="stylesheet" href="/auth/build/assets/styles.css" />
        @endif
        
    </head>
<body class="bg-zinc-950">
    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">
        {{ $slot }} 
    </div>

    @if(config('devdojo.auth.settings.branding') && !app()->isLocal())
        <a href="https://devdojo.com/auth" target="_blank" class="flex fixed bottom-0 left-1/2 z-30 justify-center items-center px-2.5 py-1.5 w-auto text-xs font-medium rounded-t-lg border -translate-x-1/2 cursor-pointer bg-zinc-900 hover:bg-zinc-800/70 text-white/40 hover:text-white/80 border-zinc-800">
            <svg class="mr-1 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 151 201" fill="none"><path fill="currentColor" fill-rule="evenodd" d="M75.847.132c-28.092 23.884-45.7 25-75 25v96.125c0 15.285 4.238 26.069 12.393 35.442l17.526-33.718.345-.661 5.06-9.74L76.496 35l40.323 77.58c20.95 2.616 30.894 8.93 30.894 8.93a219.818 219.818 0 0 0-24.117 1.321l-1.371.15c-1.345.158-2.69.326-4.017.502a227.52 227.52 0 0 0-41.712 9.705C50.36 141.907 30.44 153.7 18.4 161.993c9.303 8.615 22.183 16.475 38.353 26.344 5.927 3.616 12.296 7.503 19.093 11.795 6.796-4.292 13.165-8.179 19.091-11.795 16.494-10.066 29.564-18.042 38.907-26.861a205.398 205.398 0 0 0-35.223-19.64 225.71 225.71 0 0 1 30.106-6.358l10.533 20.272c7.627-9.153 11.586-19.721 11.586-34.493V25.132c-29.3 0-46.909-1.117-75-25Zm.649 112.615c-6.892.793-14.306 1.973-22.26 3.655l2.566-4.923 19.694-37.896 19.693 37.896c-6.582.089-13.155.513-19.693 1.268Z" clip-rule="evenodd"/></svg>
            <p>Secured by DevDojo</p>
        </a>
    @endif
</body>
</html>