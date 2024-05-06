<div x-data="{
    favicon_light: @entangle('favicon_light'),
    favicon_dark: @entangle('favicon_dark'),
}">
    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Favicon Image</label>
            <p class="text-sm leading-6 text-gray-400">Choose the default favicon image. This image will show by default and in light mode.</p>
        </div>
        <div class="flex flex-col items-start w-full h-auto">
            @if(isset($favicon_light) && $favicon_light != '')
                <div class="flex relative justify-center items-center w-12 h-12 rounded border border-zinc-200 bg-zinc-100">
                    <img src="{{ url($favicon_light) . '?' . uniqid() }}" class="w-auto h-8 rounded-md" />
                </div>
            @endif
            <label for="favicon_light" class="flex overflow-hidden justify-start items-center mt-3 w-auto h-auto text-sm bg-gray-50 rounded-md border border-gray-300 cursor-pointer hover:bg-gray-100">
                <div class="px-2 py-1.5 text-white bg-zinc-800">Upload</div>
                <div class="px-3 py-1.5 text-zinc-500 group-hover:text-zinc-600">Choose an image</div>
                <input id="favicon_light" type="file" wire:model="favicon_light" class="hidden" />
            </label>
        </div>
    </div>

    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Favicon Dark Mode Image</label>
            <p class="text-sm leading-6 text-gray-400">This is the favicon image will show when user machine is in dark mode.</p>
        </div>
        <div class="flex flex-col items-start w-full h-auto">
            @if(isset($favicon_dark) && $favicon_dark != '')
                <div class="flex relative justify-center items-center w-12 h-12 rounded border border-zinc-800 bg-zinc-900">
                    <img src="{{ url($favicon_dark) . '?' . uniqid() }}" class="w-auto h-8 rounded-md" />
                </div>
            @endif
            <label for="favicon_dark" class="flex overflow-hidden justify-start items-center mt-3 w-auto h-auto text-sm bg-gray-50 rounded-md border border-gray-300 cursor-pointer hover:bg-gray-100">
                <div class="px-2 py-1.5 text-white bg-zinc-800">Upload</div>
                <div class="px-3 py-1.5 text-zinc-500 group-hover:text-zinc-600">Choose an image</div>
                <input id="favicon_dark" type="file" wire:model="favicon_dark" class="hidden" />
            </label>
        </div>
    </div>
    
</div>