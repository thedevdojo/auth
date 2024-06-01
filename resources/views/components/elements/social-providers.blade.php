@props([
    'socialProviders' => \Devdojo\Auth\Helper::activeProviders(),
    'separator' => true,
    'separator_text' => 'or'
])
@if(count($socialProviders))
    @if($separator)
        <x-auth::elements.separator class="my-7">{{ $separator_text }}</x-auto::elements.separator>
    @endif
    <div class="relative space-y-2 w-full">
        @foreach($socialProviders as $slug => $provider)
            <x-auth::elements.social-button :$slug :$provider />
        @endforeach
    </div>
@endif