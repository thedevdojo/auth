<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    User::query()->delete();
});

/*
|--------------------------------------------------------------------------
| Password Reset Request Page Tests
|--------------------------------------------------------------------------
*/

it('displays the password reset request page', function () {
    $response = $this->get(route('auth.password.request'));

    $response->assertStatus(200);
});

it('shows validation error for invalid email format on reset request', function () {
    Livewire::test('auth.password.reset')
        ->set('email', 'not-valid-email')
        ->call('sendResetPasswordLink')
        ->assertHasErrors(['email' => 'email']);
});

it('shows validation error for empty email on reset request', function () {
    Livewire::test('auth.password.reset')
        ->set('email', '')
        ->call('sendResetPasswordLink')
        ->assertHasErrors(['email' => 'required']);
});

it('sends password reset link for valid email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test('auth.password.reset')
        ->set('email', 'test@example.com')
        ->call('sendResetPasswordLink')
        ->assertHasNoErrors()
        ->assertSet('emailSentMessage', trans(Password::RESET_LINK_SENT));

    Notification::assertSentTo($user, ResetPassword::class);
});

it('shows error for non-existent email on reset request', function () {
    Notification::fake();

    Livewire::test('auth.password.reset')
        ->set('email', 'nonexistent@example.com')
        ->call('sendResetPasswordLink')
        ->assertHasErrors(['email']);

    Notification::assertNothingSent();
});

/*
|--------------------------------------------------------------------------
| Password Reset Page (with token) HTTP Tests
| Note: These tests use HTTP requests as Volt components with Folio dynamic
| routes ([token].blade.php) can't be directly tested via Livewire::test()
|--------------------------------------------------------------------------
*/

it('displays the password reset page with valid token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::broker()->createToken($user);

    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

    $response->assertStatus(200);
    $response->assertSee('test@example.com');
});

it('displays the password reset page with invalid token but still shows form', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Page should still load even with invalid token - error shown on submit
    $response = $this->get(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]));

    $response->assertStatus(200);
});

it('resets password with valid token using password broker directly', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::broker()->createToken($user);

    // Use the password broker directly to test the reset logic
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::PASSWORD_RESET);

    // Verify password was changed
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});

it('fails to reset password with invalid token using password broker', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Use the password broker directly with invalid token
    $response = Password::broker()->reset(
        [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::INVALID_TOKEN);

    // Verify password was NOT changed
    $user->refresh();
    $this->assertTrue(Hash::check('oldpassword', $user->password));
});

it('fails to reset password with expired token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::broker()->createToken($user);

    // Delete the token to simulate expiration
    Password::broker()->deleteToken($user);

    // Use the password broker directly
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::INVALID_TOKEN);

    // Verify password was NOT changed
    $user->refresh();
    $this->assertTrue(Hash::check('oldpassword', $user->password));
});

it('fails to reset password with wrong email', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::broker()->createToken($user);

    // Use the password broker directly with wrong email
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => 'wrong@example.com',
            'password' => 'newpassword123',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::INVALID_USER);

    // Verify password was NOT changed
    $user->refresh();
    $this->assertTrue(Hash::check('oldpassword', $user->password));
});

it('cannot reuse the same token twice', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::broker()->createToken($user);

    // First reset should succeed
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::PASSWORD_RESET);

    // Verify password was changed
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));

    // Second reset with same token should fail
    $response = Password::broker()->reset(
        [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'anotherpassword',
        ],
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        },
    );

    expect($response)->toBe(Password::INVALID_TOKEN);

    // Verify password was NOT changed again
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});

it('can create multiple tokens for different users', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $token1 = Password::broker()->createToken($user1);
    $token2 = Password::broker()->createToken($user2);

    expect($token1)->not->toBe($token2);

    // Both tokens should be valid for their respective users
    $this->assertTrue(Password::broker()->tokenExists($user1, $token1));
    $this->assertTrue(Password::broker()->tokenExists($user2, $token2));

    // Tokens should not be interchangeable
    $this->assertFalse(Password::broker()->tokenExists($user1, $token2));
    $this->assertFalse(Password::broker()->tokenExists($user2, $token1));
});
