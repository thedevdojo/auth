<?php

namespace Devdojo\Auth\Http\Controllers;

use Devdojo\Auth\Models\SocialProvider;
use Devdojo\Auth\Models\SocialProviderUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class SocialController
{
    public function __construct() {}

    public function redirect(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        $provider = Socialite::driver($driver);
        $scopes = config('devdojo.auth.providers.'.$driver.'.scopes');

        // Only OAuth 2 providers have scopes, Socialite's default Twitter driver is OAuth 1.
        if (! empty($scopes)) {
            $provider->scopes((array) $scopes);
        }

        return $provider->redirect();
    }

    private function dynamicallySetSocialProviderCredentials($provider)
    {
        $socialProvider = $this->getProviderCredentialsWithOverrides($provider);

        Config::set('services.'.$provider.'.client_id', $socialProvider->client_id);
        Config::set('services.'.$provider.'.client_secret', $socialProvider->client_secret);
        Config::set('services.'.$provider.'.redirect', '/auth/'.$provider.'/callback');

    }

    private function getProviderCredentialsWithOverrides($provider)
    {
        $socialProvider = SocialProvider::where('slug', $provider)->first();

        switch ($provider) {
            case 'facebook':
                $socialProvider->client_id = sprintf('%d', $socialProvider->client_id);
                break;
        }

        return $socialProvider;
    }

    public function callback(Request $request, $driver)
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        try {
            $socialiteUser = Socialite::driver($driver)->user();
            $providerUser = $this->findOrCreateProviderUser($socialiteUser, $driver);

            if ($providerUser instanceof RedirectResponse) {
                return $providerUser; // This is an error redirect
            }

            Auth::login($providerUser->user);

            // Honor url.intended — a funnel that parked a destination before
            // sending the user through OAuth (e.g. a pending build's /launch)
            // must survive the social round trip, exactly like a password
            // login would. The configured redirect stays the fallback.
            return redirect()->intended(config('devdojo.auth.settings.redirect_after_auth'));
        } catch (\Exception $e) {
            return redirect()->route('auth.login')->with('error', 'An error occurred during authentication. Please try again.');
        }
    }

    private function findOrCreateProviderUser($socialiteUser, $driver)
    {
        $providerUser = SocialProviderUser::where('provider_slug', $driver)
            ->where('provider_user_id', $socialiteUser->getId())
            ->first();

        if ($providerUser) {
            return $providerUser;
        }

        // Validate new accounts and provider links before writing either table.
        // Existing linked identities above can still sign in if profile data changes.
        Validator::make([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
        ], [
            'name' => 'nullable|string|max:191',
            'email' => 'required|string|email|max:191',
        ])->validate();

        $user = app(config('auth.providers.users.model'))->where('email', $socialiteUser->getEmail())->first();

        // If no existing user and registrations are disabled, reject the request
        if (! $user && ! config('devdojo.auth.settings.registration_enabled', true)) {
            return redirect()->route('auth.login')->with('error',
                config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.'));
        }

        if ($user) {
            $existingProvider = $user->socialProviders()->first();
            if ($existingProvider) {
                return redirect()->route('auth.login')->with('error',
                    "This email is already associated with a {$existingProvider->provider_slug} account. Please login using that provider.");
            }
        }

        return DB::transaction(function () use ($socialiteUser, $driver, $user) {
            // The provider just vouched for this address, so an account that
            // signed up with it by email and never verified is verified now:
            // a social login lets the person straight in, new or not.
            if ($user && is_null($user->email_verified_at)) {
                $user->forceFill(['email_verified_at' => now()])->save();
                event(new Verified($user));
            }

            $user = $user ?? $this->createUser($socialiteUser);

            return $this->createSocialProviderUser($user, $socialiteUser, $driver);
        });
    }

    private function createUser($socialiteUser)
    {
        $user = app(config('auth.providers.users.model'))->create([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'email_verified_at' => now(),
        ]);

        // A social signup IS a registration — fire the framework event so
        // Registered listeners (welcome emails, funnel persistence) run for
        // this path too. The verification listener no-ops: the account is
        // created pre-verified above.
        event(new Registered($user));

        return $user;
    }

    private function createSocialProviderUser($user, $socialiteUser, $driver)
    {
        return $user->socialProviders()->create([
            'provider_slug' => $driver,
            'provider_user_id' => $socialiteUser->getId(),
            'nickname' => $socialiteUser->getNickname(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
            'provider_data' => json_encode($socialiteUser->user),
            'token' => $socialiteUser->token,
            'refresh_token' => $socialiteUser->refreshToken,
            'token_expires_at' => $socialiteUser->expiresIn ? now()->addSeconds($socialiteUser->expiresIn) : null,
        ]);
    }
}
