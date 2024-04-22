<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
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

    #[Validate('required|min:8|same:password')]
    public $passwordConfirmation = '';

    public function register()
    {
        $this->validate();

        $user = User::create([
            'email' => $this->email,
            'name' => $this->name,
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

            <x-auth::elements.heading text="Sign up" />
                
            <form wire:submit="register" class="mt-5 space-y-3">
                <x-auth::elements.input label="Name" type="text" wire:model="name" />
                <x-auth::elements.input label="Email Address" type="email" wire:model="email" />
                <x-auth::elements.input label="Password" type="password" wire:model="password" />
                <x-auth::elements.input label="Confirm Password" type="password" wire:model="passwordConfirmation" />
                
                <x-auth::elements.button type="primary" rounded="md" submit="true">Continue</x-auth::elements.button>
            </form>

            <div class="mt-3 space-x-0.5 text-sm leading-5 text-center text-gray-400 translate-y-3 dark:text-gray-300">
                <span>Already have an account?</span>
                <x-auth::elements.text-link href="{{ route('auth.login') }}">Sign in</x-auth::elements.text-link>
            </div>
        </x-auth::elements.container>
    @endvolt

</x-auth::layouts.app>