<div class="left-0 z-50 w-80 h-screen duration-300 ease-out bg-zinc-50" x-cloak>
    <div class="flex justify-between items-center px-5 py-6 w-full">
        <a href="/auth/setup" wire:navigate" class="flex items-center space-x-2 cursor-pointer group">
            <span class="text-base font-bold leading-none">Authentication <span class="font-light">Setup</span></span>
        </a>
    </div>
    <div class="hidden px-3 pt-0 pb-2">
        <a href="https://auth.devdojo.com/docs" target="_blank" class="block p-5 text-xs bg-white rounded-xl border duration-300 ease-out hover:shadow-md opacity-[0.98] hover:opacity-100 border-zinc-200">
            <span class="flex flex-col">
                <span class="font-semibold">Learn about configs & setup</span>
                <span class="underline">Visit the documenation here</span>
            </span>
        </a>
    </div>
    <x-auth::setup.sidebar-links></x-auth::setup.sidebar-links>
</div>
<div x-show="sidebar" @click="sidebar=false" class="fixed z-40 w-screen h-screen bg-black/50" x-cloak></div>