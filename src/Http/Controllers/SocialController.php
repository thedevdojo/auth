<?php

namespace Devdojo\Auth\Http\Controllers;

use Devdojo\Auth\Models\SocialProvider;
use Devdojo\Auth\Models\SocialProviderUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController
{
    public function __construct() {}

    public function redirect(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        return Socialite::driver($driver)->redirect();
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

            return redirect()->to(config('devdojo.auth.settings.redirect_after_auth'));
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

        $user = app(config('auth.providers.users.model'))->where('email', $socialiteUser->getEmail())->first();

        if ($user) {
            $existingProvider = $user->socialProviders()->first();
            if ($existingProvider) {
                return redirect()->route('auth.login')->with('error',
                    "This email is already associated with a {$existingProvider->provider_slug} account. Please login using that provider.");
            }
        }

        return DB::transaction(function () use ($socialiteUser, $driver, $user) {
            $user = $user ?? $this->createUser($socialiteUser);

            return $this->createSocialProviderUser($user, $socialiteUser, $driver);
        });
    }

    private function createUser($socialiteUser)
    {
        return app(config('auth.providers.users.model'))->create([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'email_verified_at' => now(),
        ]);
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
