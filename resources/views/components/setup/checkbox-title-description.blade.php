<label class="inline-flex items-start space-x-2 cursor-pointer select-none">
    <div class="relative translate-y-0">
        <input type="checkbox" {{ $attributes }} class="sr-only peer">
        <div class="relative w-8 h-[18px] bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-gradient-to-r peer-checked:from-blue-600 peer-checked:to-indigo-600"></div>
    </div>
    <span class="relative">
        <span class="block text-sm font-medium leading-tight text-gray-900">{{ $title ?? '' }}</span>
        @if(($description ?? false))
            <span class="text-sm leading-tight text-gray-400">{{ $description }}</span>
        @endif
    </span>
</label>
