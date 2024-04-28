<button class="flex justify-start items-start p-8 bg-white rounded-lg border duration-300 ease-out text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50 border-zinc-200 hover:border-zinc-300 group">
    <div class="flex-shrink-0 w-20 h-20">
        @include('auth::includes.setup.icons.' . ($icon ?? 'social-providers'))
    </div>
    
    <div class="flex flex-col items-start ml-5">
        <h3 class="mb-2 text-lg font-semibold text-zinc-700">{{ $title ?? '' }}</h3>
        <p class="mb-4 text-sm text-left">{{ $description ?? '' }}</p>
    </div>
</button>