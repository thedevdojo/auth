<li>
    <a href="{{ $href ?? '' }}" class="flex gap-x-2 items-center px-2 py-1.5 text-sm font-medium leading-6 rounded-md text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 group">
        {{-- <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-[0.625rem] font-medium bg-white text-zinc-400 border-zinc-200 group-hover:border-indigo-600 group-hover:text-indigo-600">H</span> --}}
        
        @if($icon ?? false)
            <x-dynamic-component class="w-4 h-4 shrink-0" component="phosphor-{{ $icon }}-duotone" />
        @endif
        
        <span class="truncate opacity-80">{{ $text ?? '' }}</span>
    </a>
</li>