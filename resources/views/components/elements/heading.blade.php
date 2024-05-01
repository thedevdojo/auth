@props([
    'align' => 'center',
    'text' => 'Heading Text',
    'description' => '',
    'show_subheadline' => false
])

<div @class([
        'flex flex-col sm:mx-auto sm:w-full sm:max-w-md',
        'items-center' => $align == 'center',
        'items-start' => $align == 'left'
    ])
>
    <div @class([
        'flex flex-col w-full',
        'items-center' => $align == 'center',
        'items-start' => $align == 'left',
    ])
>
        <x-auth::elements.logo
         :height="config('devdojo.auth.appearance.logo.height')"
         :isImage="(config('devdojo.auth.appearance.logo.type') == 'image')"
         :imageSrc="config('devdojo.auth.appearance.logo.image_src')"
         :svgString="config('devdojo.auth.appearance.logo.svg_string')"
         />
    </div>
    <h1 id="auth-heading" class="mt-1 text-xl font-medium leading-9 text-gray-800 dark:text-gray-200">{{ $text ?? '' }}</h1>
    @if(($description ?? false) && $show_subheadline)
        <p class="mb-1.5 space-x-0.5 text-sm leading-5 text-center text-gray-500 dark:text-gray-400">{{ $description ?? '' }}</p>
    @endif
</div>