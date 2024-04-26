<?php

use App\Models\User;
use Devdojo\Auth\Models\SocialProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Devdojo\Auth\Helper;
use function Laravel\Folio\{middleware, name};

middleware(['guest']);
name('auth.register');

new class extends Component
{
    #[Validate('required')]
    public $name = '';

    #[Validate('required|email|unique:users')]
    public $email = '';

    #[Validate('required|min:8|same:passwordConfirmation')]
    public $password = '';

    public $customizations = [];

    public function mount(){
        $this->customizations = config('devdojo.auth.customizations');
        $this->social_providers = Helper::activeProviders();
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'email' => $this->email,
            //'name' => $this->name,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->intended('/');
    }
};

?>

<x-auth::layouts.app>

    @volt('auth.register')
        <x-auth::elements.container>

            <x-auth::elements.heading 
                :text="($customizations['register']['text']['headline'] ?? 'No Heading')" 
                :align="($customizations['heading']['align'] ?? 'center')" 
                :description="($customizations['register']['text']['subheadline'] ?? 'No Description')"
                :show_subheadline="($customizations['register']['show_subheadline'] ?? false)" />
                
            <form wire:submit="register" class="mt-5 space-y-5">
                {{-- <x-auth::elements.input label="Name" type="text" wire:model="name" /> --}}
                <x-auth::elements.input label="Email Address" type="email" wire:model="email" autofocus="true" />
                {{-- <x-auth::elements.input label="Password" type="password" wire:model="password" /> --}}
                
                <x-auth::elements.button type="primary" rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>

            <div class="mt-3 space-x-0.5 text-sm leading-5 text-left text-gray-400 dark:text-gray-300">
                <span>Already have an account?</span>
                <x-auth::elements.text-link href="{{ route('auth.login') }}">Sign in</x-auth::elements.text-link>
            </div>

            @if(count($this->social_providers))
                <x-auth::elements.separator class="my-7">or</x-auto::elements.separator>
                <div class="relative space-y-2 w-full">
                    @foreach($this->social_providers as $provider)
                        <x-auth::elements.social-button :$provider />    
                    @endforeach
                </div>
            @endif


        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>