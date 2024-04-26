<?php

/*
 * These are some default authentication settings
 */
return [
    'dev' => env('DEVDOJO_AUTH_DEV', false),
    'branding' => env('DEVDOJO_AUTH_BRANDING', true),
    'redirect_after_auth' => '/dashboard'
];