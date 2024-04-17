<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- <script src="/auth/build/assets/scripts.js"></script> --}}
        {{-- <link rel="stylesheet" href="/auth/build/assets/styles.css" /> --}}
        <!-- Scripts -->
        @vite(['packages/devdojo/auth/resources/css/auth.css', 'packages/devdojo/auth/resources/css/auth.js'])
    </head>
<body class="bg-gray-50 dark:bg-zinc-950">
    {{ $slot }}
</body>
</html>