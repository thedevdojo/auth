<?php

use Illuminate\Support\Facades\Gate;

it('allows access to setup pages in local environment', function () {
    // Simulate local environment
    app()->detectEnvironment(fn () => 'local');

    $this->get('/auth/setup')
        ->assertStatus(200);
});

it('denies access to setup pages in production without permission', function () {
    // Simulate production environment
    app()->detectEnvironment(fn () => 'production');

    // Gate returns false by default
    $this->get('/auth/setup')
        ->assertStatus(403);
});

it('allows access to setup pages in production with viewAuthSetup permission', function () {
    // Simulate production environment
    app()->detectEnvironment(fn () => 'production');

    // Grant permission via Gate
    Gate::define('viewAuthSetup', fn ($user = null) => true);

    $this->get('/auth/setup')
        ->assertStatus(200);
});

it('protects all setup routes with middleware', function () {
    // Simulate production environment without permission
    app()->detectEnvironment(fn () => 'production');

    $setupRoutes = [
        '/auth/setup',
        '/auth/setup/appearance',
        '/auth/setup/providers',
        '/auth/setup/language',
        '/auth/setup/settings',
    ];

    foreach ($setupRoutes as $route) {
        $this->get($route)
            ->assertStatus(403, "Route {$route} should be protected");
    }
});
