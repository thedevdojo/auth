<?php

use App\Models\User;
use Devdojo\Auth\Http\Controllers\SocialController;
use Devdojo\Auth\Models\SocialProviderUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('new user created via social has verified email', function () {
    // Test the createUser method's behavior
    $controller = new SocialController;

    // Use reflection to access private createUser method
    $method = new ReflectionMethod(SocialController::class, 'createUser');
    $method->setAccessible(true);

    // Create a mock Socialite user
    $mockSocialiteUser = new class
    {
        public function getName()
        {
            return 'Test User';
        }

        public function getEmail()
        {
            return 'test@example.com';
        }
    };

    $user = $method->invoke($controller, $mockSocialiteUser);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->not->toBeNull();
});

it('existing unverified user is verified when linking social account', function () {
    // Create a user without verified email
    $user = User::create([
        'name' => 'Existing User',
        'email' => 'existing@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => null,
    ]);

    expect($user->email_verified_at)->toBeNull();

    $controller = new SocialController;

    // Use reflection to access the private findOrCreateProviderUser method
    $method = new ReflectionMethod(SocialController::class, 'findOrCreateProviderUser');
    $method->setAccessible(true);

    // Create mock Socialite user with same email
    $mockSocialiteUser = new class
    {
        public function getId()
        {
            return '123456789';
        }

        public function getName()
        {
            return 'Existing User';
        }

        public function getEmail()
        {
            return 'existing@example.com';
        }

        public function getNickname()
        {
            return 'existinguser';
        }

        public function getAvatar()
        {
            return 'https://example.com/avatar.jpg';
        }

        public $token = 'test-token';

        public $refreshToken = 'test-refresh-token';

        public $expiresIn = 3600;

        public $user = ['id' => '123456789', 'email' => 'existing@example.com'];
    };

    $result = $method->invoke($controller, $mockSocialiteUser, 'google');

    // Refresh and verify email is now verified
    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
    expect($result)->toBeInstanceOf(SocialProviderUser::class);
});

it('already verified user preserves original verification timestamp when linking social', function () {
    // Create a user with verified email
    $verifiedAt = now()->subDays(10);
    $user = User::create([
        'name' => 'Verified User',
        'email' => 'verified@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => $verifiedAt,
    ]);

    $user->refresh();
    $originalVerifiedAt = $user->email_verified_at->getTimestamp();

    $controller = new SocialController;

    // Use reflection to access the private findOrCreateProviderUser method
    $method = new ReflectionMethod(SocialController::class, 'findOrCreateProviderUser');
    $method->setAccessible(true);

    // Create mock Socialite user with same email
    $mockSocialiteUser = new class
    {
        public function getId()
        {
            return '123456789';
        }

        public function getName()
        {
            return 'Verified User';
        }

        public function getEmail()
        {
            return 'verified@example.com';
        }

        public function getNickname()
        {
            return 'verifieduser';
        }

        public function getAvatar()
        {
            return 'https://example.com/avatar.jpg';
        }

        public $token = 'test-token';

        public $refreshToken = 'test-refresh-token';

        public $expiresIn = 3600;

        public $user = ['id' => '123456789', 'email' => 'verified@example.com'];
    };

    $method->invoke($controller, $mockSocialiteUser, 'google');

    // Refresh and verify original timestamp is preserved
    $user->refresh();
    expect($user->email_verified_at->getTimestamp())->toBe($originalVerifiedAt);
});
