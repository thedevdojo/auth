<?php

return [
    'relying_party_id' => env('PASSKEYS_RELYING_PARTY_ID', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),
    'relying_party_name' => env('PASSKEYS_RELYING_PARTY_NAME', env('APP_NAME', 'Laravel')),
    'origins' => array_values(array_filter([
        env('PASSKEYS_ORIGIN', env('APP_URL')),
    ])),
    'challenge_expiration' => env('PASSKEYS_CHALLENGE_EXPIRATION', 60),
    'guard' => env('PASSKEYS_GUARD', 'web'),
    'routes_middleware' => ['web'],
    'management_middleware' => ['web', 'auth'],
    'throttle' => 'passkeys',
    'redirect' => env('PASSKEYS_REDIRECT', config('devdojo.auth.settings.redirect_after_auth', '/')),
];
