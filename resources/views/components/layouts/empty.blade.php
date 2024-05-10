<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('auth::includes.head')
</head>
<body>
    {{ $slot }}
</body>
</html>