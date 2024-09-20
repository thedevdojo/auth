<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    // Ensure each test starts with a clean slate
    User::query()->delete();
});

it('validates email and password fields', function () {
    Livewire::test('auth.register')
        ->set('email', 'invalid-email')
        ->set('password', '123')
        ->call('register')
        ->assertHasErrors(['email' => 'email', 'password' => 'min']);
});

it('registers a new user and logs in', function () {
    $this->withoutExceptionHandling();
    $this->mock(Registered::class);
    config()->set('devdojo.auth.settings.registration_include_name_field', true);
    config()->set('devdojo.auth.settings.registration_require_email_verification', false);

    Livewire::test('auth.register')
        ->set('email', 'user@example.com')
        ->set('password', 'secret1234')
        ->set('name', 'John Doe')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertTrue(Auth::check());
    $this->assertEquals('user@example.com', Auth::user()->email);
    $this->assertEquals('John Doe', Auth::user()->name);
});

it('conditionally displays name and password fields based on configuration', function () {
    config()->set('devdojo.auth.settings.registration_include_name_field', true);
    config()->set('devdojo.auth.settings.registration_show_password_same_screen', true);

    Livewire::test('auth.register')
        ->assertSet('showNameField', true)
        ->assertSet('showPasswordField', true)
        ->assertSeeHtml('wire:model="name"')
        ->assertSeeHtml('wire:model="password"')
        ->assertDontSeeHtml('wire:model="password_confirmation"');
});

it('checks for required fields and validation errors', function () {
    Livewire::test('auth.register')
        ->set('email', 'not-an-email')
        ->call('register')
        ->assertHasErrors(['email' => 'email']);

    Livewire::test('auth.register')
        ->set('password', 'short')
        ->call('register')
        ->assertHasErrors(['password' => 'min']);
});

it('validates password confirmation field', function () {
    config()->set('devdojo.auth.settings.registration_include_password_confirmation_field', true);

    Livewire::test('auth.register')
        ->set('password', 'secret1234')
        ->set('password_confirmation', 'differentpassword')
        ->call('register')
        ->assertHasErrors(['password' => 'confirmed']);
});

it('conditionally displays password confirmation field based on configuration', function () {
    config()->set('devdojo.auth.settings.registration_include_password_confirmation_field', true);
    config()->set('devdojo.auth.settings.registration_show_password_same_screen', true);

    Livewire::test('auth.register')
        ->assertSet('showPasswordField', true)
        ->assertSet('showPasswordConfirmationField', true)
        ->assertSeeHtml('wire:model="password_confirmation"');
});

it('registers a new user with password confirmation and logs in', function () {
    $this->withoutExceptionHandling();
    $this->mock(Registered::class);
    config()->set('devdojo.auth.settings.registration_include_password_confirmation_field', true);
    config()->set('devdojo.auth.settings.registration_require_email_verification', false);

    Livewire::test('auth.register')
        ->set('email', 'user@example.com')
        ->set('password', 'secret1234')
        ->set('password_confirmation', 'secret1234')
        ->set('name', 'John Doe')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertTrue(Auth::check());
    $this->assertEquals('user@example.com', Auth::user()->email);
});

it('renders social login buttons if providers are available', function () {
    config()->set('devdojo.auth.providers', [
        'google' => [
            'name' => 'Google',
            'active' => true,
            'label' => 'Google',
        ],
        'facebook' => [
            'name' => 'Facebook',
            'active' => true,
            'label' => 'Facebook',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'active' => false,
            'label' => 'Twitter',
        ],
    ]);

    Livewire::test('auth.register')
        ->assertSee('Google')
        ->assertSee('Facebook')
        ->assertDontSee('Twitter');
});
