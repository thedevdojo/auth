<li {{ $attributes }}>
    <a href="{{ url($pageLink) }}" @if(!($newTab ?? false)){{ 'wire:navigate' }}@else{{ 'target="_blank' }}@endif class="flex w-full gap-x-2 items-center px-2 py-1.5 text-sm font-medium leading-6 rounded-md @if(Request::is($pageLink)){{ 'bg-zinc-200/70 text-zinc-900' }}@else{{ 'hover:bg-zinc-200/70 hover:text-zinc-900 text-zinc-500' }}@endif group">
        
        @if($icon ?? false)
            <x-dynamic-component class="w-4 h-4 shrink-0" component="phosphor-{{ $icon }}-duotone" />
        @endif
        
        <span class="truncate opacity-80">{{ $text ?? '' }}</span>
    </a>
</li>