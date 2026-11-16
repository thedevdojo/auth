<?php

use Laravel\Socialite\Facades\Socialite;

it('adds the configured scopes to the provider redirect', function () {
    config(['devdojo.auth.providers.google.scopes' => ['https://www.googleapis.com/auth/calendar.readonly']]);

    $location = $this->get('/auth/google/redirect')->assertRedirect()->headers->get('Location');

    expect(urldecode($location))->toContain('https://www.googleapis.com/auth/calendar.readonly')->toContain('openid');
});

it('does not set scopes when none are configured', function () {
    // A plain mock throws if scopes() is called, like Socialite's OAuth 1 Twitter driver.
    $provider = Mockery::mock();
    $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://example.test/oauth'));
    Socialite::shouldReceive('driver')->with('twitter')->once()->andReturn($provider);

    $this->get('/auth/twitter/redirect')->assertRedirect('https://example.test/oauth');
});
