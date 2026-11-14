<?php

namespace Devdojo\Auth\Traits;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;

/**
 * The login flow, as a trait any Livewire component can wear.
 *
 * The package's own `auth/login` page uses it, and so can an app's modal
 * or inline form — the state names (`email`, `password`, `rememberMe`,
 * `showPasswordField`, `showSocialProviderInfo`…) are the page's, so a
 * view written against the page works against the trait.
 *
 * Two shapes: the page's email-first flow (the password step only shows
 * once the email is known, which is how a social-only account learns it
 * has no password) and a same-screen flow (email + password together —
 * `login_show_password_same_screen`, or override `passwordOnSameScreen()`),
 * where the social-only check runs on submit instead. Both end in
 * `authenticated()`, the hook to override for a different landing.
 */
trait AuthenticatesUsers
{
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    #[Validate('bool')]
    public $rememberMe = false;

    public $showPasswordField = false;

    public $showIdentifierInput = true;

    public $showSocialProviderInfo = false;

    public $twoFactorEnabled = true;

    public $userSocialProviders = [];

    public function mountAuthenticatesUsers(): void
    {
        $this->twoFactorEnabled = (bool) config('devdojo.auth.settings.enable_2fa');

        if ($this->passwordOnSameScreen()) {
            $this->showPasswordField = true;
        }
    }

    /** Back to the email: on the email-first flow this hides the password step again. */
    public function editIdentity(): void
    {
        if ($this->showPasswordField && ! $this->passwordOnSameScreen()) {
            $this->showPasswordField = false;

            return;
        }

        $this->showIdentifierInput = true;
        $this->showSocialProviderInfo = false;
    }

    public function authenticate()
    {
        if (! $this->showPasswordField) {
            $this->validateOnly('email');

            $user = $this->findUserByEmail();

            if ($user && $this->showSocialOnlyAccount($user)) {
                return;
            }

            if (config('devdojo.auth.settings.check_account_exists_before_login') && is_null($user)) {
                $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-email', {})); }, 10);");
                $this->addError('email', trans(config('devdojo.auth.language.login.couldnt_find_your_account')));

                return;
            }

            $this->showPasswordField = true;
            $this->js("setTimeout(function(){ window.dispatchEvent(new CustomEvent('focus-password', {})); }, 10);");

            return;
        }

        $this->validate();

        $user = $this->findUserByEmail();

        if ($this->passwordOnSameScreen() && $user && $this->showSocialOnlyAccount($user)) {
            return;
        }

        if ($this->hasTooManyLoginAttempts()) {
            return;
        }

        $credentials = ['email' => $this->email, 'password' => $this->password];

        event(new Attempting('web', $credentials, false));

        if (! isset($user->id) || ! Auth::validate($credentials)) {
            RateLimiter::hit($this->throttleKey());
            event(new Failed('web', null, $credentials));
            $this->addError('password', trans('auth.failed'));

            return;
        }

        RateLimiter::clear($this->throttleKey());

        if ($this->twoFactorEnabled && ! is_null($user->two_factor_confirmed_at)) {
            session()->put(['login.id' => $user->getKey()]);

            return redirect()->route('auth.two-factor-challenge');
        }

        if (! Auth::attempt($credentials, $this->rememberMe)) {
            event(new Failed('web', null, $credentials));
            $this->addError('password', trans('auth.failed'));

            return;
        }

        return $this->authenticated($user);
    }

    /** Where a signed-in user goes. Override for a different landing. */
    protected function authenticated($user)
    {
        session()->regenerate();

        $to = config('devdojo.auth.settings.redirect_after_auth');

        if (session()->get('url.intended') != route('logout.get')) {
            return redirect()->intended($to);
        }

        return redirect($to);
    }

    /** Wrong passwords allowed per email and IP address before the form locks for a minute. */
    protected function maxLoginAttempts(): int
    {
        return 5;
    }

    protected function hasTooManyLoginAttempts(): bool
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxLoginAttempts())) {
            return false;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $this->addError('password', trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]));

        return true;
    }

    protected function throttleKey(): string
    {
        return 'devdojo-auth.login:'.Str::transliterate(Str::lower($this->email)).'|'.request()->ip();
    }

    /** Email and password on one screen (true) or the page's email-first flow (false). */
    protected function passwordOnSameScreen(): bool
    {
        return (bool) config('devdojo.auth.settings.login_show_password_same_screen', false);
    }

    /**
     * An account that only ever signed in through a social provider has no
     * password to type: show which provider(s) it uses instead of a field.
     */
    protected function showSocialOnlyAccount($user): bool
    {
        if (! is_null($user->password)) {
            return false;
        }

        $this->userSocialProviders = $user->socialProviders->pluck('provider_slug')->all();
        $this->showIdentifierInput = false;
        $this->showSocialProviderInfo = true;

        return true;
    }

    protected function findUserByEmail()
    {
        return app(config('auth.providers.users.model'))->where('email', $this->email)->first();
    }
}
