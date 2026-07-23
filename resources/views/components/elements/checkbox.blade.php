@props([
    'label' => null,
    'name' => null,
    'id' => null,
])

<label for="{{ $id ?? '' }}" class="flex items-center space-x-2 text-sm font-medium select-none cursor-pointer text-zinc-600">
    <input type="checkbox" {{ $attributes->whereStartsWith('wire:model') }} id="{{ $id ?? '' }}" name="{{ $name ?? '' }}" class="sr-only peer">
    <span class="flex shrink-0 justify-center items-center w-5 h-5 rounded border-2 border-zinc-400 bg-white peer-checked:border-zinc-800 peer-checked:bg-zinc-800 [&_svg]:scale-0 peer-checked:[&_svg]:scale-100">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    </span>
    <span class="peer-checked:text-zinc-900">{{ $label ?? '' }}</span>
</label>
