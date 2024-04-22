<button {{ $attributes->merge(['class' => 'flex items-center px-3 py-2 text-xs font-medium bg-white rounded-lg ease-out duration-300 border cursor-pointer select-none hover:bg-zinc-200 border-zinc-200']) }}>
    <x-dynamic-component component="phosphor-{{ $icon ?? '' }}" class="mr-1 -ml-0.5 w-3 h-3" />
    <span>{{ $slot }}</span>
</button>