<?php

/*
 * These are some default authentication settings
 */
return [
    'redirect_after_auth' => '/dashboard',
    'registration_show_password_same_screen' => true,
    'registration_include_name_field' => false,
    'registration_require_email_verification' => false,
    'enable_branding' => env('DEVDOJO_AUTH_BRANDING', true),
    'dev_mode' => env('DEVDOJO_AUTH_DEV', false)
];