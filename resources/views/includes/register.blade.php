<div class="relative w-full">

    <x-auth::elements.container>

        <x-auth::elements.heading text="Sign up" />
            
            <form wire:submit="register" class="mt-5 space-y-3">
                @foreach(config('devdojo.auth.pages.register.fields') as $field)
                    <x-auth::elements.input :label="$field['name']" :type="$field['type']" wire:model="{{ $field['model'] }}" />
                @endforeach
                
                <x-auth::elements.button type="primary" rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>

            <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                <span>Already have an account?</span>
                <x-auth::elements.text-link href="{{ route('auth.login') }}">Sign in</x-auth::elements.text-link>
            </div>
    </x-auth::elements.container>
</div>