<?php

/*
 * These are some default authentication settings
 */
return [
    'dev' => env('DEVDOJO_AUTH_DEV', false),
    'branding' => env('DEVDOJO_AUTH_BRANDING', true),
    'redirect_after_auth' => '/dashboard',
    'registration_show_password_same_screen' => true,
    'registration_include_name_field' => false,
    'registration_require_email_verification' => false
];