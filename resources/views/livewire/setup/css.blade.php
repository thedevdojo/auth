<div x-data="{
    css: @entangle('css')
}"
x-init=""
@update-css-code.window="css=event.detail.value;"
    >
    <div class="mb-3 w-full" wire:ignore>
        <textarea id="css-editor" class="w-full min-h-[350px] rounded-xl border border-zinc-200 overflow-hidden" x-model="css"></textarea>
    </div>
    <x-auth::setup.button wire:click="update">Update</x-auth::setup.button>
</div>