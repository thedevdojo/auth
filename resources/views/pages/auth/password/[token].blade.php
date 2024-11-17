<?php

use Devdojo\Auth\Traits\HasConfigs;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

name('password.reset');

new class extends Component
{
    use HasConfigs;

    #[Validate('required')]
    public $token;
    #[Validate('required|email')]
    public $email;
    #[Validate('required|min:8|same:passwordConfirmation')]
    public $password;
    public $passwordConfirmation;

    public function mount($token)
    {
        $this->loadConfigs();
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
            <x-auth::elements.heading
                :text="($language->passwordReset->headline ?? 'No Heading')"
                :description="($language->passwordReset->subheadline ?? 'No Description')"
                :show_subheadline="($language->passwordReset->show_subheadline ?? false)"
            />

            <form wire:submit="resetPassword" class="space-y-5">
                <x-auth::elements.input :label="config('devdojo.auth.language.passwordReset.email')" type="email" id="email" name="email" data-auth="email-input" wire:model="email" autofocus="true" />
                <x-auth::elements.input :label="config('devdojo.auth.language.passwordReset.password')" type="password" id="password" name="password" data-auth="password-input" wire:model="password" autocomplete="new-password" />
                <x-auth::elements.input :label="config('devdojo.auth.language.passwordReset.password_confirm')" type="password" id="password_confirmation" name="password_confirmation" data-auth="password-confirm-input" wire:model="passwordConfirmation" autocomplete="new-password" />
                <x-auth::elements.button type="primary" data-auth="submit-button" rounded="md" submit="true">{{config('devdojo.auth.language.passwordReset.button')}}</x-auth::elements.button>
            </form>
        </x-auth::elements.container>
    @endvolt
</x-auth::layouts.app>
