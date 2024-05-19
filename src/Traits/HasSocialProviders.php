<?php

namespace Devdojo\Auth\Traits;

use Devdojo\Auth\Models\SocialProviderUser;

trait HasSocialProviders
{
    /**
     * Relationship with SocialProviderUser.
     */
    public function socialProviders()
    {
        return $this->hasMany(SocialProviderUser::class);
    }

    /**
     * Retrieve a list of social providers linked to the user.
     */
    public function getLinkedSocialProvidersAttribute()
    {
        return $this->socialProviders->map(function ($providerUser) {
            return $providerUser->socialProvider;
        });
    }

    /**
     * Get social provider user data for a specific provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     * @return SocialProviderUser|null
     */
    public function getSocialProviderUser($providerSlug)
    {
        return $this->socialProviders->firstWhere('provider_slug', $providerSlug);
    }

    /**
     * Check if the user is linked to a specific social provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     * @return bool
     */
    public function hasSocialProvider($providerSlug)
    {
        return $this->getSocialProviderUser($providerSlug) !== null;
    }

    /**
     * Add or update social provider user information for a given provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     * @param  array  $data  Data to store/update for the provider.
     * @return SocialProviderUser
     */
    public function addOrUpdateSocialProviderUser($providerSlug, array $data)
    {
        $providerUser = $this->getSocialProviderUser($providerSlug);

        if ($providerUser) {
            $providerUser->update($data);
        } else {
            $data['provider_slug'] = $providerSlug;
            $providerUser = new SocialProviderUser($data);
            $this->socialProviders()->save($providerUser);
        }

        return $providerUser;
    }
}
