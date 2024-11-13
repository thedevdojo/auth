<?php

use App\Models\User;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    // Ensure each test starts with a clean slate
    User::query()->delete();
});

test('Two factor challenge page redirects to login for guest user', function () {
    $this->get('auth/two-factor-challenge')
        ->assertRedirect('auth/login');
});

test('Two factor challenge page redirects if user is logged in and they don\'t have the login.id session', function () {
    withANewUser()->get('auth/two-factor-challenge')
        ->assertRedirect('auth/login');
});

test('User logs in when two factor disabled, the login.id session should not be created', function () {
    config()->set('devdojo.auth.settings.enable_2fa', false);
    $user = createUser(['password' => \Hash::make('password123'), 'two_factor_confirmed_at' => now()]);

    Livewire::test('auth.login')
        ->set('email', $user->email)
        ->set('showPasswordField', true)
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(config('devdojo.auth.settings.redirect_after_auth'));

    $this->assertTrue(! Session::has('login.id'));
});

test('User logs in when two factor enabled, the login.id session should be created', function () {
    config()->set('devdojo.auth.settings.enable_2fa', true);
    $user = createUser(['password' => \Hash::make('password123'), 'two_factor_confirmed_at' => now()]);

    Livewire::test('auth.login')
        ->set('email', $user->email)
        ->set('showPasswordField', true)
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect('auth/two-factor-challenge');

    $this->assertTrue(Session::has('login.id'));
});

test('User logs in without 2FA, they should not be redirected to auth/two-factor-challenge page', function () {
    config()->set('devdojo.auth.settings.enable_2fa', true);
    $user = createUser(['password' => \Hash::make('password123')]);

    Livewire::test('auth.login')
        ->set('email', $user->email)
        ->set('showPasswordField', true)
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(config('devdojo.auth.settings.redirect_after_auth'));
});

it('user cannot view two factor challenge page logging in if it\'s disabled', function () {
    config()->set('devdojo.auth.settings.enable_2fa', false);
    $user = loginAsUser();
    $this->get('user/two-factor-authentication')
        ->assertRedirect('/');
});

it('user can view two factor challenge page when it\'s enabled', function () {
    config()->set('devdojo.auth.settings.enable_2fa', true);
    $user = loginAsUser();
    $this->get('user/two-factor-authentication')
        ->assertOk();
});
