<?php

namespace Devdojo\Auth\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Devdojo\Auth\Models\SocialProvider;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Devdojo\Auth\Models\SocialProviderUser;

class SocialController extends Controller
{
    public function __construct(){
        
    }

    public function redirect(Request $request, $driver)
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
                                                    ->where('provider_user_id', $socialiteUser->id)
                                                    ->first();

            if ($socialProviderUser) {
                // Log the user in and redirect to the home page
                Auth::login($socialProviderUser->user);
                return redirect()->to(config('devdojo.auth.settings.redirect_after_auth'));
            }

            // Check if the email from the social provider already exists in the User table
            $user = User::where('email', $socialiteUser->email)->first();

            if ($user) {
                // Inform the user that an account with this email already exists
                throw new \Exception('An account with the provided email already exists. Please log in.');
            }

            // No user exists, register a new user
            $newUser = User::create([
                'name' => $socialiteUser->name,
                'email' => $socialiteUser->email
                // Add other fields as necessary
            ]);

            $newUser->email_verified_at = now();
            $newUser->save();

            // Now add the social provider info for this new user
            $newUser->addOrUpdateSocialProviderUser($driver, [
                'provider_user_id' => $socialiteUser->id,
                'nickname' => $socialiteUser->nickname,
                'name' => $socialiteUser->name,
                'email' => $socialiteUser->email,
                'avatar' => $socialiteUser->avatar,
                'provider_data' => json_encode($socialiteUser->user),
                'token' => $socialiteUser->token,
                'refresh_token' => $socialiteUser->refreshToken,
                'token_expires_at' => now()->addSeconds($socialiteUser->expiresIn)
            ]);

            // Log in the newly created user
            Auth::login($newUser);
        });

        // Redirect to a specific page after successful registration and login
        return redirect()->to(config('devdojo.auth.settings.redirect_after_auth')); // Adjust according to your needs
    }

    private function dynamicallySetSocialProviderCredentials($provider){
        $socialProvider = SocialProvider::where('slug', $provider)->first();

        if(app()->isLocal()){
            Config::set('services.' . $provider . '.client_id', $socialProvider->client_id_dev);
            Config::set('services.' . $provider . '.client_secret', $socialProvider->client_secret_dev);
        } else {
            Config::set('services.' . $provider . '.client_id', $socialProvider->client_id_prod);
            Config::set('services.' . $provider . '.client_secret', $socialProvider->client_secret_prod);
        }

        Config::set('services.' . $provider . '.redirect', '/auth/' . $provider . '/callback');

    }
}
