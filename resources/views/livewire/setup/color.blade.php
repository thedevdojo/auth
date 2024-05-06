<div
    x-data="{
        text_color: @entangle('text_color'),
        button_color: @entangle('button_color'),
        button_text_color: @entangle('button_text_color'),
        input_text_color: @entangle('input_text_color'),
        input_border_color: @entangle('input_border_color')
    }"
 class="max-w-xl">
    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Text Color</label>
        </div>
        <div class="w-full h-auto">
            <input type="color" value="#000000" wire:model.live="text_color" />
        </div>
    </div>
    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Button Color</label>
        </div>
        <div class="w-full h-auto">
            <input type="color" value="#000000" wire:model.live="button_color" />
        </div>
    </div>
    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Button Text Color</label>
        </div>
        <div class="w-full h-auto">
            <input type="color" value="#000000" wire:model.live="button_text_color" />
        </div>
    </div>
    <div class="pb-5 mb-5 border-b border-zinc-200">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Input Text Color</label>
        </div>
        <div class="w-full h-auto">
            <input type="color" value="#000000" wire:model.live="input_text_color" />
        </div>
    </div>
    <div class="pb-5">
        <div class="pb-3 w-full">
            <label class="block text-sm font-medium leading-6 text-gray-900">Input Border Color</label>
        </div>
        <div class="w-full h-auto">
            <input type="color" value="#000000" wire:model.live="input_border_color" />
        </div>
    </div>
    
</div>