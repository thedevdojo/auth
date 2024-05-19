<?php

namespace Devdojo\Auth\Http\Controllers;

use Devdojo\Auth\Models\User;
use Devdojo\Auth\Models\SocialProvider;
use Devdojo\Auth\Models\SocialProviderUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialController
{
    public function __construct()
    {

    }

    public function redirect(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request, $driver)
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        $socialiteUser = Socialite::driver($driver)->user();

        DB::transaction(function () use ($socialiteUser, $driver) {
            // Attempt to find the user based on the social provider's ID and slug
            $socialProviderUser = SocialProviderUser::where('provider_slug', $driver)
                ->where('provider_user_id', $socialiteUser->getId())
                ->first();

            if ($socialProviderUser) {
                // Log the user in and redirect to the home page
                Auth::login($socialProviderUser->user);

                return redirect()->to(config('devdojo.auth.settings.redirect_after_auth'));
            }

            // Check if the email from the social provider already exists in the User table
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // Inform the user that an account with this email already exists
                throw new \Exception('An account with the provided email already exists. Please log in.');
            }

            // No user exists, register a new user
            $newUser = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                // Add other fields as necessary
            ]);

            $newUser->email_verified_at = now();
            $newUser->save();

            // Now add the social provider info for this new user
            $newUser->addOrUpdateSocialProviderUser($driver, [
                'provider_user_id' => $socialiteUser->getId(),
                'nickname' => $socialiteUser->getNickname(),
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'avatar' => $socialiteUser->getAvatar(),
                'provider_data' => json_encode($socialiteUser->user),
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'token_expires_at' => now()->addSeconds($socialiteUser->expiresIn),
            ]);

            // Log in the newly created user
            Auth::login($newUser);
        });

        // Redirect to a specific page after successful registration and login
        return redirect()->to(config('devdojo.auth.settings.redirect_after_auth')); // Adjust according to your needs
    }

    private function dynamicallySetSocialProviderCredentials($provider)
    {
        $socialProvider = SocialProvider::where('slug', $provider)->first();

        Config::set('services.'.$provider.'.client_id', $socialProvider->client_id);
        Config::set('services.'.$provider.'.client_secret', $socialProvider->client_secret);
        Config::set('services.'.$provider.'.redirect', '/auth/'.$provider.'/callback');

    }
}
