<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <x-auth::elements.link href="/">
        <x-auth::elements.logo class="mx-auto w-auto h-9 fill-current text-zinc-900 dark:text-gray-100" />
    </x-auth::elements.link>
    <h1 id="auth-heading" class="mt-1 text-2xl font-medium leading-9 text-center text-gray-800 dark:text-gray-200">{{ $text ?? '' }}</h1>
    @if($description ?? false)
        <p class="mb-1.5 space-x-0.5 text-sm leading-5 text-center text-gray-500 dark:text-gray-400">{{ $description ?? '' }}</p>
    @endif
</div>