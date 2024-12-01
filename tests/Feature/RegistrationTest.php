<?php

use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    config()->set('devdojo.auth.settings.registration_enabled', true);
    config()->set('devdojo.auth.settings.enable_email_registration', true);
});

it('allows access to registration page when enabled', function () {
    Livewire::test('auth.register')
        ->assertOk()
        ->assertDontSee('Registrations are currently disabled');
});

it('redirects to login when registrations are disabled', function () {
    config()->set('devdojo.auth.settings.registration_enabled', false);

    Livewire::test('auth.register')
        ->assertRedirect(route('auth.login'));

    expect(session('error'))->toBe(
        config('devdojo.auth.language.register.registrations_disabled', 'Registrations are currently disabled.')
    );
});

it('allows registration when enabled', function () {
    $component = Livewire::test('auth.register')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('name', 'Test User')
        ->call('register');

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->email)->toBe('test@example.com');
});

it('preserves other registration settings when enabled', function () {
    config()->set('devdojo.auth.settings.registration_include_name_field', true);
    config()->set('devdojo.auth.settings.registration_show_password_same_screen', true);

    $component = Livewire::test('auth.register');

    expect($component->get('showNameField'))->toBeTrue();
    expect($component->get('showPasswordField'))->toBeTrue();
});

it('hides email registration form when email registration is disabled', function () {
    config()->set('devdojo.auth.settings.enable_email_registration', false);

    $component = Livewire::test('auth.register');

    expect($component->get('showEmailRegistration'))->toBeFalse();
    expect($component->get('showEmailField'))->toBeFalse();
    expect($component->get('showPasswordField'))->toBeFalse();
    expect($component->get('showNameField'))->toBeFalse();
});

it('shows email registration form when email registration is enabled', function () {
    config()->set('devdojo.auth.settings.enable_email_registration', true);

    $component = Livewire::test('auth.register');

    expect($component->get('showEmailRegistration'))->toBeTrue();
    expect($component->get('showEmailField'))->toBeTrue();
});

it('prevents email registration when disabled', function () {
    config()->set('devdojo.auth.settings.enable_email_registration', false);

    $component = Livewire::test('auth.register')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('register');

    expect(Auth::check())->toBeFalse();
    expect(session('error'))->toBe(
        config('devdojo.auth.language.register.email_registration_disabled', 'Email registration is currently disabled. Please use social login.')
    );
});

it('validates empty rules when email registration is disabled', function () {
    config()->set('devdojo.auth.settings.enable_email_registration', false);

    $component = Livewire::test('auth.register');

    expect($component->instance()->rules())->toBeEmpty();
});

it('preserves social login functionality when email registration is disabled', function () {
    config()->set('devdojo.auth.settings.enable_email_registration', false);

    config()->set('devdojo.auth.providers', [
        'google' => ['name' => 'Google', 'active' => true],
        'facebook' => ['name' => 'Facebook', 'active' => false],
    ]);

    Livewire::test('auth.register')
        ->assertSee('Google');
});
