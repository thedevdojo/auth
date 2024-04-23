@props([
    'name' => 'Name',
    'network' => 'Not Defined'
])
<button class="flex items-center px-[18px] py-3 space-x-3.5 w-full h-auto text-sm rounded-md border border-zinc-200 text-zinc-600 hover:bg-zinc-100">
    <span class="w-5 h-5">
        @include("auth::includes.social-icons.$network")
    </span>
    
    <span>Continue with {{ $name }}</span>
</button>