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
        'md' => 'px-4 py-2 text-sm font-medium rounded-' . $rounded,
        'lg' => 'px-5 py-3  text-sm font-medium rounded-' . $rounded,
        'xl' => 'px-6 py-3.5 text-base font-medium rounded-' . $rounded,
        '2xl' => 'px-7 py-4 text-base font-medium rounded-' . $rounded
    };
@endphp

@php
    $typeClasses = match ($type) {
        'primary' => 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-700 hover:bg-zinc-950 dark:focus:ring-offset-zinc-900 dark:focus:ring-zinc-100 dark:hover:bg-white dark:hover:text-zinc-800 focus:ring-2 focus:ring-zinc-950 focus:ring-offset-2',
        'secondary' => 'bg-white border text-zinc-500 hover:text-zinc-700 border-zinc-200/70 dark:focus:ring-offset-zinc-900 dark:border-zinc-400/10 hover:bg-zinc-50 active:bg-white dark:focus:ring-zinc-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-200/60 dark:bg-zinc-800/50 dark:hover:bg-zinc-800/70 dark:text-zinc-400 focus:shadow-outline',
        'success' => 'bg-green-600 text-white hover:bg-green-600/90 focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 focus:bg-green-700/90 focus:ring-green-700',
        'info' => 'bg-blue-600 text-white hover:bg-blue-600/90 focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 focus:bg-blue-700/90 focus:ring-blue-700',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-500/90 focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 focus:bg-amber-600/90 focus:ring-amber-600',
        'danger' => 'bg-red-600 text-white hover:bg-red-600/90 focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 focus:bg-red-700/90 focus:ring-red-700',
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

<{!! $tagAttr !!} {!! $attributes->except(['class']) !!} class="{{ $sizeClasses }} {{ $typeClasses }} cursor-pointer inline-flex items-center w-full justify-center disabled:opacity-50 font-semibold focus:outline-none">
    <svg xmlns="http://www.w3.org/2000/svg" wire:loading viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 w-4 h-4 animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
    {{ $slot }}
</{{ $tagClose }}>