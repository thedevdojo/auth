<div class="mb-5">
    <div class="flex justify-between items-center w-full">
        <a href="/auth/setup" class="inline-flex items-center px-4 py-1.5 mb-3 space-x-1 text-sm font-medium rounded-full group bg-zinc-100 text-zinc-600 hover:text-zinc-800">
            <x-phosphor-arrow-left-bold class="w-3 h-3 duration-300 ease-out translate-x-0 group-hover:-translate-x-0.5" />
            <span>Back</span>
        </a>
        <button @click="preview=true" href="/auth/setup" class="inline-flex items-center px-4 py-1.5 mb-3 space-x-1 text-sm font-medium rounded-full group bg-zinc-100 text-zinc-600 hover:text-zinc-800">
            <x-phosphor-monitor-bold class="w-4 h-4 duration-300 ease-out translate-x-0 group-hover:-translate-x-0.5" />
            <span>Preview</span>
        </button>
    </div>
    <h2 class="mb-2 text-2xl font-bold text-left">{{ $title ?? '' }}</h2>
    <p class="text-sm text-left text-gray-600">{{ $description ?? '' }}</p>
</div>