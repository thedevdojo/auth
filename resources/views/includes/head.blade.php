<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? 'Auth' }}</title>
@if(config('devdojo.auth.settings.dev_mode'))
    @vite(['packages/devdojo/auth/resources/css/auth.css', 'packages/devdojo/auth/resources/css/auth.js'])
@else
    <script src="/auth/build/assets/scripts.js"></script>
    <link rel="stylesheet" href="/auth/build/assets/styles.css" />
@endif

@php
    $buttonRGBColor = \Devdojo\Auth\Helper::convertHexToRGBString(config('devdojo.auth.appearance.color.button'));
    $inputBorderRGBColor = \Devdojo\Auth\Helper::convertHexToRGBString(config('devdojo.auth.appearance.color.input_border'));
@endphp
<style>
    .auth-component-button:focus{
        --tw-ring-opacity: 1; --tw-ring-color: rgb({{ $buttonRGBColor }} / var(--tw-ring-opacity));
    }
    .auth-component-input{
        color: {{ config('devdojo.auth.appearance.color.input_text') }}
    }
    .auth-component-input:focus, .auth-component-code-input:focus{
        --tw-ring-color: rgb({{ $inputBorderRGBColor }} / var(--tw-ring-opacity));
        border-color: rgb({{ $inputBorderRGBColor }} / var(--tw-border-opacity));
    }
    .auth-component-input-label-focused{
        color: {{ config('devdojo.auth.appearance.color.input_border') }}
    }
</style>

@if(file_exists(public_path('auth/app.css')))
    <link rel="stylesheet" href="/auth/app.css" />
@endif

<link href="{{ url(config('devdojo.auth.appearance.favicon.light')) }}" rel="icon" media="(prefers-color-scheme: light)" />
<link href="{{ url(config('devdojo.auth.appearance.favicon.dark')) }}" rel="icon" media="(prefers-color-scheme: dark)" />

@stack('devdojo-auth-head-scripts')
