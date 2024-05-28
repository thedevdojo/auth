<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

use function Laravel\Folio\name;

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('password.reset');

new class extends Component
{
    #[Validate('required')]
    public $token;

    #[Validate('required|email')]
    public $email;

    #[Validate('required|min:8|same:passwordConfirmation')]
    public $password;
    public $passwordConfirmation;

    public function mount($token)
    {
        $this->email = request()->query('email', '');
        $this->token = $token;
    }

    public function resetPassword()
    {
        $this->validate();

        $response = Password::broker()->reset(
            [
                'token' => $this->token,
                'email' => $this->email,
                'password' => $this->password,
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);

                $user->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                Auth::guard()->login($user);
            },
        );

        if ($response == Password::PASSWORD_RESET) {
            session()->flash(trans($response));

            return redirect('/');
        }

        $this->addError('email', trans($response));
    }
};

?>

<x-auth::layouts.app>
    @volt('auth.password.token')
        <x-auth::elements.container>
            <x-auth::elements.heading text="Reset password" />
            
            <form wire:submit="resetPassword" class="mt-5 space-y-5">
                <x-auth::elements.input label="Email address" type="email" id="email" name="email" wire:model="email" autofocus="true" />
                <x-auth::elements.input label="Password" type="password" id="password" name="password" wire:model="password" />
                <x-auth::elements.input label="Confirm Password" type="password" id="password_confirmation" name="password_confirmation" wire:model="passwordConfirmation" />
                <x-auth::elements.button type="primary" rounded="md" submit="true">Reset password</x-auth::elements.button>
            </form>
        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>
