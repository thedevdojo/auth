<?php

use App\Models\User;
use Devdojo\Auth\Rules\PasswordStrength;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Volt;

beforeEach(function () {
    config()->set('devdojo.auth.settings.registration_enabled', true);
    config()->set('devdojo.auth.settings.enable_email_registration', true);
    config()->set('devdojo.auth.settings.registration_show_password_same_screen', true);
    config()->set('devdojo.auth.settings.password_min_length', 8);
    config()->set('devdojo.auth.settings.password_require_uppercase', false);
    config()->set('devdojo.auth.settings.password_require_numeric', false);
    config()->set('devdojo.auth.settings.password_require_special_character', false);
    config()->set('devdojo.auth.settings.password_require_uncompromised', false);
});

afterEach(function () {
    User::where('email', 'like', '%@test.com')->delete();
});

it('returns Password rule instance from PasswordStrength::rule()', function () {
    $rule = PasswordStrength::rule();
    expect($rule)->toBeInstanceOf(Password::class);
});

it('returns array of rules from PasswordStrength::rules()', function () {
    $rules = PasswordStrength::rules();
    expect($rules)->toBeArray();
    expect($rules[0])->toBe('required');
    expect($rules[1])->toBeInstanceOf(Password::class);
});

it('includes confirmed rule when parameter is true', function () {
    $rules = PasswordStrength::rules(true);
    expect($rules)->toContain('confirmed');
});

it('does not include confirmed rule when parameter is false', function () {
    $rules = PasswordStrength::rules(false);
    expect($rules)->not->toContain('confirmed');
});

it('enforces minimum password length from config', function () {
    config()->set('devdojo.auth.settings.password_min_length', 12);

    $response = Volt::test('auth.register')
        ->set('email', 'minlength@test.com')
        ->set('password', 'short123')
        ->call('register');

    $response->assertHasErrors(['password']);
});

it('allows password meeting minimum length', function () {
    config()->set('devdojo.auth.settings.password_min_length', 8);

    $response = Volt::test('auth.register')
        ->set('email', 'validlength@test.com')
        ->set('password', 'password123')
        ->call('register');

    $response->assertHasNoErrors(['password']);
});

it('requires uppercase when config enabled', function () {
    config()->set('devdojo.auth.settings.password_require_uppercase', true);

    $response = Volt::test('auth.register')
        ->set('email', 'uppercase@test.com')
        ->set('password', 'lowercase123')
        ->call('register');

    $response->assertHasErrors(['password']);
});

it('allows password with uppercase when required', function () {
    config()->set('devdojo.auth.settings.password_require_uppercase', true);

    $response = Volt::test('auth.register')
        ->set('email', 'uppercase2@test.com')
        ->set('password', 'Uppercase123')
        ->call('register');

    $response->assertHasNoErrors(['password']);
});

it('requires numeric when config enabled', function () {
    config()->set('devdojo.auth.settings.password_require_numeric', true);

    $response = Volt::test('auth.register')
        ->set('email', 'numeric@test.com')
        ->set('password', 'nonumbers')
        ->call('register');

    $response->assertHasErrors(['password']);
});

it('allows password with numbers when required', function () {
    config()->set('devdojo.auth.settings.password_require_numeric', true);

    $response = Volt::test('auth.register')
        ->set('email', 'numeric2@test.com')
        ->set('password', 'password123')
        ->call('register');

    $response->assertHasNoErrors(['password']);
});

it('requires special character when config enabled', function () {
    config()->set('devdojo.auth.settings.password_require_special_character', true);

    $response = Volt::test('auth.register')
        ->set('email', 'special@test.com')
        ->set('password', 'NoSpecial123')
        ->call('register');

    $response->assertHasErrors(['password']);
});

it('allows password with special character when required', function () {
    config()->set('devdojo.auth.settings.password_require_special_character', true);

    $response = Volt::test('auth.register')
        ->set('email', 'special2@test.com')
        ->set('password', 'Special@123')
        ->call('register');

    $response->assertHasNoErrors(['password']);
});

it('enforces all password requirements together', function () {
    config()->set('devdojo.auth.settings.password_min_length', 10);
    config()->set('devdojo.auth.settings.password_require_uppercase', true);
    config()->set('devdojo.auth.settings.password_require_numeric', true);
    config()->set('devdojo.auth.settings.password_require_special_character', true);

    $response = Volt::test('auth.register')
        ->set('email', 'allreqs@test.com')
        ->set('password', 'StrongPass@123')
        ->call('register');

    $response->assertHasNoErrors(['password']);
});

it('fails when any requirement is not met', function () {
    config()->set('devdojo.auth.settings.password_require_uppercase', true);
    config()->set('devdojo.auth.settings.password_require_numeric', true);
    config()->set('devdojo.auth.settings.password_require_special_character', true);

    $response = Volt::test('auth.register')
        ->set('email', 'partialreqs@test.com')
        ->set('password', 'password123')
        ->call('register');

    $response->assertHasErrors(['password']);
});

it('uses default min length of 8 when config not set', function () {
    config()->set('devdojo.auth.settings.password_min_length', null);

    $response = Volt::test('auth.register')
        ->set('email', 'default@test.com')
        ->set('password', '1234567')
        ->call('register');

    $response->assertHasErrors(['password']);

    $response2 = Volt::test('auth.register')
        ->set('email', 'default2@test.com')
        ->set('password', 'eightchr')
        ->call('register');

    $response2->assertHasNoErrors(['password']);
});

it('shows password requirements on registration page when enabled', function () {
    config(['devdojo.auth.settings.password_show_requirements' => true]);
    config(['devdojo.auth.settings.password_min_length' => 10]);
    config(['devdojo.auth.settings.registration_show_password_same_screen' => true]);

    $response = $this->get(route('auth.register'));

    $response->assertOk();
    $response->assertSee('Be at least 10 characters');
});

it('hides password requirements when disabled', function () {
    config(['devdojo.auth.settings.password_show_requirements' => false]);
    config(['devdojo.auth.settings.password_min_length' => 10]);
    config(['devdojo.auth.settings.registration_show_password_same_screen' => true]);

    $response = $this->get(route('auth.register'));

    $response->assertOk();
    $response->assertDontSee('Be at least 10 characters');
});

it('shows uppercase requirement when enabled', function () {
    config(['devdojo.auth.settings.password_show_requirements' => true]);
    config(['devdojo.auth.settings.password_require_uppercase' => true]);
    config(['devdojo.auth.settings.registration_show_password_same_screen' => true]);

    $response = $this->get(route('auth.register'));

    $response->assertOk();
    $response->assertSee('Include uppercase and lowercase letters');
});

it('shows numeric requirement when enabled', function () {
    config(['devdojo.auth.settings.password_show_requirements' => true]);
    config(['devdojo.auth.settings.password_require_numeric' => true]);
    config(['devdojo.auth.settings.registration_show_password_same_screen' => true]);

    $response = $this->get(route('auth.register'));

    $response->assertOk();
    $response->assertSee('Include at least one number');
});

it('shows special character requirement when enabled', function () {
    config(['devdojo.auth.settings.password_show_requirements' => true]);
    config(['devdojo.auth.settings.password_require_special_character' => true]);
    config(['devdojo.auth.settings.registration_show_password_same_screen' => true]);

    $response = $this->get(route('auth.register'));

    $response->assertOk();
    $response->assertSee('Include at least one special character');
});
