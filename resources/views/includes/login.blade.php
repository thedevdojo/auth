<div class="w-full">
    
    <x-auth::elements.container>
        
            <x-auth::elements.heading :text="$authData['heading'] ?? ''" />
            
            <form wire:submit="authenticate" class="mt-5 space-y-3">
                
                @foreach(config('devdojo.auth.pages.login.fields') as $field)
                    <x-auth::elements.input :label="$field['name']" :type="$field['type']" wire:model="{{ $field['model'] }}" />
                @endforeach

                <div class="flex justify-between items-center mt-6 text-sm leading-5">
                    <x-auth::elements.text-link href="{{ route('auth.password.request') }}">Forgot your password?</x-auth::elements.text-link>
                </div>

                <x-auth::elements.button type="primary" rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>
            
            
            <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 dark:text-gray-300">
                <span>Don't have an account?</span>
                <x-auth::elements.text-link href="{{ route('auth.register') }}">Sign up</x-auth::elements.text-link>
            </div>

    </x-auth::elements.container>
</div>