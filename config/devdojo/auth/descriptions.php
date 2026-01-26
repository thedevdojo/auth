<?php

/*
 * Branding configs for your application
 */
return [
    'settings' => [
        'redirect_after_auth' => 'Where should the user be redirected to after they are authenticated?',
        'redirect_after_logout' => 'Where should the user be redirected to after they log out?',
        'registration_enabled' => 'Enable or disable registration functionality. If disabled, users will not be able to register for an account.',
        'registration_show_password_same_screen' => 'During registrations, show the password on the same screen or show it on an individual screen.',
        'registration_include_name_field' => 'During registration, include the Name field.',
        'registration_include_password_confirmation_field' => 'During registration, include the Password Confirmation field.',
        'registration_require_email_verification' => 'During registration, require users to verify their email.',
        'password_min_length' => 'Minimum password length required (default: 8).',
        'password_require_uppercase' => 'Require at least one uppercase letter in passwords.',
        'password_require_numeric' => 'Require at least one number in passwords.',
        'password_require_special_character' => 'Require at least one special character (!@#$%^&* etc.) in passwords.',
        'password_require_uncompromised' => 'Check passwords against the Have I Been Pwned database to prevent compromised passwords.',
        'enable_branding' => 'This will toggle on/off the Auth branding at the bottom of each auth screen. Consider leaving on to support and help grow this project.',
        'dev_mode' => 'This is for development mode, when set in Dev Mode Assets will be loaded from Vite',
        'enable_2fa' => 'Enable the ability for users to turn on Two Factor Authentication',
        'enable_email_registration' => 'Enable the ability for users to register via email',
        'login_show_social_providers' => 'Show the social providers login buttons on the login form',
        'center_align_social_provider_button_content' => 'Center align the content in the social provider button?',
        'center_align_text' => 'Center align text?',
        'social_providers_location' => 'The location of the social provider buttons (top or bottom)',
        'check_account_exists_before_login' => 'Determines if the system checks for account existence before login',
    ],
];
