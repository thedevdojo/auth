<x-auth::layouts.app title="{{ config('devdojo.auth.language.login.page_title') }}">
    @volt('auth.preview.login') 
        @include('auth::includes.pages.login')
    @endvolt
</x-auth::layouts.app>