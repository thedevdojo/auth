<div class="flex justify-between items-center px-5 py-2.5 select-none hover:bg-zinc-100" onclick="if(event.target == this){ this.querySelector('input[type=checkbox]').click(); }">
    <span class="flex items-center space-x-1.5 text-sm font-medium text-zinc-500"> 
        <x-dynamic-component component="phosphor-{{ $icon ?? '' }}-duotone" class="w-4 h-4" />
        <span>{{ $text ?? '' }}</span>
    </span>
    <x-auth::setup.checkbox />
</div>