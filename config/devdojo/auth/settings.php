<?php

/*
 * These are some default authentication settings
 */
return [
    'redirect_after_auth' => '/dashboard',
    'registration_show_password_same_screen' => false,
    'registration_include_name_field' => false,
    'registration_require_email_verification' => false,
    'branding' => env('DEVDOJO_AUTH_BRANDING', true),
    'dev' => env('DEVDOJO_AUTH_DEV', false)
];