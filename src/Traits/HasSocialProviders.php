<?php

namespace Devdojo\Auth\Traits;

use Devdojo\Auth\Models\SocialProviderUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSocialProviders
{
    /**
     * Relationship with SocialProviderUser.
     */
    public function socialProviders(): HasMany
    {
        return $this->hasMany(SocialProviderUser::class);
    }

    /**
     * Retrieve a list of social providers linked to the user.
     */
    public function getLinkedSocialProvidersAttribute(): Collection
    {
        return collect($this->socialProviders->get())->map(function (SocialProviderUser $providerUser) {
            return $providerUser->socialProvider;
        });
    }

    /**
     * Get social provider user data for a specific provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     */
    public function getSocialProviderUser(string $providerSlug): ?SocialProviderUser
    {
        return $this->socialProviders->firstWhere('provider_slug', $providerSlug);
    }

    /**
     * Check if the user is linked to a specific social provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     */
    public function hasSocialProvider(string $providerSlug): bool
    {
        return $this->getSocialProviderUser($providerSlug) !== null;
    }

    /**
     * Add or update social provider user information for a given provider.
     *
     * @param  string  $providerSlug  The slug of the social provider.
     * @param  array<string, mixed>  $data  Data to store/update for the provider.
     */
    public function addOrUpdateSocialProviderUser(string $providerSlug, array $data): SocialProviderUser
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
