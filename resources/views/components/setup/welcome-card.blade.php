<a href="{{ url($link) }}" wire:navigate class="flex justify-start items-start px-8 py-7 bg-white rounded-lg border duration-300 ease-out text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50 border-zinc-200 hover:border-zinc-300 group">
    <span class="flex-shrink-0 w-20 h-20">
        @include('auth::includes.setup.icons.' . ($icon ?? 'social-providers'))
    </span>
    
    <span class="flex flex-col items-start ml-5">
        <span class="mb-2 text-lg font-semibold text-zinc-700">{{ $title ?? '' }}</span>
        <span class="mb-1 text-sm text-left">{{ $description ?? '' }}</span>
    </span>
</a>