<?php

use App\Models\User;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

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
    $user = createUser(['password' => Hash::make('password123'), 'two_factor_confirmed_at' => now()]);

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
    $user = createUser(['password' => Hash::make('password123'), 'two_factor_confirmed_at' => now()]);

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
    $user = createUser(['password' => Hash::make('password123')]);

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

function challengedTwoFactorUser(): array
{
    config()->set('devdojo.auth.settings.enable_2fa', true);
    $secret = (new Google2FA)->generateSecretKey();
    $user = createUser([
        'password' => Hash::make('password123'),
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode(['abcdefghij-klmnopqrst'])),
        'two_factor_confirmed_at' => now(),
    ]);
    Session::put('login.id', $user->id);

    return [$user, $secret];
}

it('logs in with a valid authentication code', function () {
    [$user, $secret] = challengedTwoFactorUser();

    Livewire::test('auth.two-factor-challenge')
        ->call('submitCode', (new Google2FA)->getCurrentOtp($secret))
        ->assertHasNoErrors()
        ->assertRedirect(config('devdojo.auth.settings.redirect_after_auth'));

    $this->assertAuthenticatedAs($user);
});

it('does not accept the same authentication code twice', function () {
    [$user, $secret] = challengedTwoFactorUser();
    $code = (new Google2FA)->getCurrentOtp($secret);

    Livewire::test('auth.two-factor-challenge')->call('submitCode', $code)->assertHasNoErrors();
    auth()->logout();

    Session::put('login.id', $user->id);
    Livewire::test('auth.two-factor-challenge')
        ->call('submitCode', $code)
        ->assertHasErrors('auth_code');

    $this->assertGuest();
});

it('stops accepting codes after too many wrong attempts', function () {
    [$user, $secret] = challengedTwoFactorUser();
    $component = Livewire::test('auth.two-factor-challenge');

    foreach (range(1, 5) as $attempt) {
        $component->call('submitCode', '000000')->assertHasErrors('auth_code');
    }

    $component->call('submitCode', (new Google2FA)->getCurrentOtp($secret))->assertHasErrors('auth_code');
    $this->assertGuest();
});

it('rejects an authentication code that is not a six digit string', function ($code) {
    challengedTwoFactorUser();

    Livewire::test('auth.two-factor-challenge')
        ->call('submitCode', $code)
        ->assertHasErrors('auth_code');

    $this->assertGuest();
})->with([[true], [['123456']], ['12345a']]);

it('sends the user back to login when there is no pending challenge', function () {
    Livewire::test('auth.two-factor-challenge')
        ->call('submitCode', '123456')
        ->assertRedirect(route('auth.login'));

    $this->assertGuest();
});
