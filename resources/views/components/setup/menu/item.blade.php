<a href="{{ $href ?? '' }}" @click="menuBarOpen=false" class="relative flex justify-start items-center w-full cursor-default select-none group space-x-1.5 rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
    @if($icon ?? false)
        <x-dynamic-component class="w-4 h-4 shrink-0" component="phosphor-{{ $icon }}-duotone" />
    @endif
        
    <span>{{ $text ?? '' }}</span>
</a>