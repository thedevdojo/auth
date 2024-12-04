@props([
    'socialProviders' => \Devdojo\Auth\Helper::activeProviders(),
    'separator' => true,
    'separator_text' => 'or'
])
@if(count($socialProviders))
    @if($separator && config('devdojo.auth.settings.social_providers_location') != 'top')
        <x-auth::elements.separator class="my-6">{{ $separator_text }}</x-auth::elements.separator>
    @endif
    <div class="relative space-y-2 w-full @if(config('devdojo.auth.settings.social_providers_location') != 'top' && !$separator){{ 'mt-3' }}@endif">
        @foreach($socialProviders as $slug => $provider)
            <x-auth::elements.social-button :$slug :$provider />
        @endforeach
    </div>
    @if($separator && config('devdojo.auth.settings.social_providers_location') == 'top')
        <x-auth::elements.separator class="my-6">{{ $separator_text }}</x-auth::elements.separator>
    @endif
@endif
