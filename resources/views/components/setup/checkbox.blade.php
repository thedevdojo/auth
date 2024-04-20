<label class="inline-flex items-center cursor-pointer">
    <input type="checkbox" onclick="event.stopPropagation()" {{ $attributes }} value="" class="sr-only peer">
    <div class="relative w-8 h-[18px] bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3.5 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-gradient-to-r peer-checked:from-blue-600 peer-checked:to-indigo-600"></div>
    @if($text ?? false)
        <span class="text-sm font-medium text-gray-900 ms-1 dark:text-gray-300">{{ $text ?? '' }}</span>
    @endif
</label>
