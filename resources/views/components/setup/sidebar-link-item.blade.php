<li>
    <button wire:click="setPage('{{ $pageLink }}'); sidebar=false;" class="flex w-full gap-x-2 items-center px-2 py-1.5 text-sm font-medium leading-6 rounded-md @if($pageLink == $page){{ 'bg-blue-600 text-white' }}@else{{ 'hover:bg-blue-600 hover:text-white text-zinc-500' }}@endif group">
        {{-- <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-[0.625rem] font-medium bg-white text-zinc-400 border-zinc-200 group-hover:border-indigo-600 group-hover:text-indigo-600">H</span> --}}
        
        @if($icon ?? false)
            <x-dynamic-component class="w-4 h-4 shrink-0" component="phosphor-{{ $icon }}-duotone" />
        @endif
        
        <span class="truncate opacity-80">{{ $text ?? '' }}</span>
    </button>
</li>