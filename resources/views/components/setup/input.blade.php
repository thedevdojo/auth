@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
    'description' => ''
])

@php $wireModel = $attributes->get('wire:model'); @endphp

<div>
    @if($label)
        <label for="{{ $id ?? '' }}" class="block text-sm font-medium leading-6 text-gray-900">{{ $label  }}</label>
    @endif

    @if($description ?? false)
        <p class="text-sm leading-6 text-gray-400">{{ $description ?? '' }}</p>
    @endif

    <div data-model="{{ $wireModel }}" class="mt-1.5 max-w-sm rounded-md shadow-sm">
        <input {{ $attributes->merge(['class' => 'appearance-none flex w-full h-10 px-3 py-2 text-sm bg-white border rounded-md border-gray-300 ring-offset-background placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-gray-300 dark:focus:border-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200/80 disabled:cursor-not-allowed disabled:opacity-50']) }} {{ $attributes->whereStartsWith('wire:model') }} id="{{ $id ?? '' }}" name="{{ $name ?? '' }}" type="{{ $type ?? '' }}" required autofocus />
    </div>

    @error($wireModel)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>