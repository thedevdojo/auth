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
    <div class="flex flex-col justify-center items-stretch py-10 w-screen min-h-screen sm:items-center">

        <x-auth::devdojoauth.heading text="Reset password" />
        
        <x-auth::devdojoauth.container>
            @volt('auth.password.token')
                <form wire:submit="resetPassword" class="space-y-6">
                    <x-auth::devdojoauth.input label="Email address" type="email" id="email" name="email" wire:model="email" />
                    <x-auth::devdojoauth.input label="Password" type="password" id="password" name="password" wire:model="password" />
                    <x-auth::devdojoauth.input label="Confirm Password" type="password" id="password_confirmation" name="password_confirmation" wire:model="passwordConfirmation" />
                    <x-auth::devdojoauth.button type="primary" rounded="md" submit="true">Reset password</x-auth::devdojoauth.button>
                </form>
            @endvolt
        </x-auth::devdojoauth.container>
    </div>
</x-auth::layouts.app>
