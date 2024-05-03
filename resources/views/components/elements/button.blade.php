@props([
    'type' => 'primary', 
    'size' => 'md', 
    'tag' => 'button',
    'href' => '/',
    'submit' => false,
    'rounded' => 'full'
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'px-2.5 py-1.5 text-xs font-medium rounded-' . $rounded,
        'md' => 'px-4 py-2.5 text-sm font-medium rounded-' . $rounded,
        'lg' => 'px-5 py-3  text-sm font-medium rounded-' . $rounded,
        'xl' => 'px-6 py-3.5 text-base font-medium rounded-' . $rounded,
        '2xl' => 'px-7 py-4 text-base font-medium rounded-' . $rounded
    };
@endphp

@php
switch ($tag ?? 'button') {
    case 'button':
        $tagAttr = ($submit) ? 'button type="submit"' : 'button type="button"';
        $tagClose = 'button';
        break;
    case 'a':
        $link = $href ?? '';
        $tagAttr = 'a  href="' . $link . '"';
        $tagClose = 'a';
        break;
    default:
        $tagAttr = 'button type="button"';
        $tagClose = 'button';
        break;
}
@endphp

<{!! $tagAttr !!} {!! $attributes->except(['class']) !!} class="{{ $sizeClasses }} auth-button opacity-[95%] hover:opacity-100 focus:ring-2  focus:ring-offset-2 cursor-pointer inline-flex items-center w-full justify-center disabled:opacity-50 font-semibold focus:outline-none" style="color:{{ config('devdojo.auth.appearance.color.button_text') }}; background-color:{{ config('devdojo.auth.appearance.color.button') }};">
    <svg xmlns="http://www.w3.org/2000/svg" wire:loading viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 w-4 h-4 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
    {{ $slot }}
</{{ $tagClose }}>