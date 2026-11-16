<?php

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;

beforeEach(function () {
    config()->set('devdojo.auth.settings.registration_require_email_verification', true);
    $this->user = createUser(['email_verified_at' => null]);
    $this->actingAs($this->user);
});

it('renders an accessible code form on the verification notice', function () {
    $this->get('/auth/verify')->assertOk()
        ->assertSee('wire:submit="verifyCode"', false)
        ->assertSee('autocomplete="one-time-code"', false)
        ->assertSee('inputmode="numeric"', false)
        ->assertSee('Verification code');
});

it('verifies the signed in user and follows the intended redirect', function () {
    Event::fake([Verified::class]);
    $code = $this->user->issueEmailVerificationCode();
    session()->put('url.intended', '/settings');

    Volt::test('auth.verify')->set('code', $code)->call('verifyCode')
        ->assertHasNoErrors()->assertRedirect('/settings');

    expect($this->user->fresh()->email_verified_at)->not->toBeNull()
        ->and($this->user->emailVerificationCode()->exists())->toBeFalse();
    Event::assertDispatchedTimes(Verified::class, 1);
});

it('uses the configured redirect after code verification', function () {
    config()->set('devdojo.auth.settings.redirect_after_auth', '/dashboard');
    Volt::test('auth.verify')->set('code', $this->user->issueEmailVerificationCode())
        ->call('verifyCode')->assertRedirect('/dashboard?verified=1');
});

it('rejects malformed codes without consuming an attempt', function ($value) {
    $this->user->issueEmailVerificationCode();
    Volt::test('auth.verify')->set('code', $value)->call('verifyCode')->assertHasErrors('code');
    expect($this->user->fresh()->email_verified_at)->toBeNull()
        ->and($this->user->emailVerificationCode()->first()->attempts)->toBe(0);
})->with(['empty' => [''], 'short' => ['12345'], 'letters' => ['abcdef'], 'array' => [['123456']]]);

it('reports invalid codes and locks further guesses at the configured limit', function () {
    Event::fake([Verified::class]);
    config()->set('devdojo.auth.settings.verification_code_max_attempts', 2);
    $code = $this->user->issueEmailVerificationCode();
    $wrong = $code === '123456' ? '654321' : '123456';
    $component = Volt::test('auth.verify')->set('code', $wrong)->call('verifyCode')
        ->assertHasErrors('code')->assertSee('This verification code is invalid.');
    $component->call('verifyCode')->assertSee('Too many incorrect attempts.');
    $component->set('code', $code)->call('verifyCode')->assertHasErrors('code');
    expect($this->user->fresh()->email_verified_at)->toBeNull();
    Event::assertNotDispatched(Verified::class);
});

it('reports expired codes and clears errors when a fresh email is requested', function () {
    Notification::fake();
    $code = $this->user->issueEmailVerificationCode();
    $this->user->emailVerificationCode()->update(['expires_at' => now()->subMinute()]);
    Volt::test('auth.verify')->set('code', $code)->call('verifyCode')
        ->assertSee('This verification code has expired.')
        ->call('resend')->assertHasNoErrors()->assertSet('code', '')->assertDispatched('resent');
    Notification::assertCount(1);
    expect($this->user->fresh()->email_verified_at)->toBeNull();
});

it('requires authentication when the session ends before submitting', function () {
    $component = Volt::test('auth.verify');
    auth()->logout();
    $component->set('code', '123456')->call('verifyCode')->assertRedirect(route('auth.login'));
    expect($this->user->fresh()->email_verified_at)->toBeNull();
});

it('keeps the signed email link working', function () {
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
        'id' => $this->user->getKey(), 'hash' => sha1($this->user->email),
    ]);
    $this->get($url)->assertRedirect();
    expect($this->user->fresh()->email_verified_at)->not->toBeNull();
});

it('does not verify another account using its code', function () {
    $other = createUser(['email_verified_at' => null]);
    Volt::test('auth.verify')->set('code', $other->issueEmailVerificationCode())
        ->call('verifyCode')->assertHasErrors('code');
    expect($this->user->fresh()->email_verified_at)->toBeNull()
        ->and($other->fresh()->email_verified_at)->toBeNull();
});

it('does not dispatch verification again for an already verified account', function () {
    Event::fake([Verified::class]);
    $code = $this->user->issueEmailVerificationCode();
    $component = Volt::test('auth.verify')->set('code', $code)->call('verifyCode');
    $component->call('verifyCode')->assertRedirect();
    Event::assertDispatchedTimes(Verified::class, 1);
});
