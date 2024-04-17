<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <x-auth::devdojoauth.link href="/">
        <x-auth::devdojoauth.logo class="mx-auto w-auto h-9 text-gray-700 fill-current dark:text-gray-100" />
    </x-auth::devdojoauth.link>
    <h2 class="mt-2 text-2xl font-medium leading-9 text-center text-gray-800 dark:text-gray-200">{{ $text ?? '' }}</h2>
</div>