<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    User::query()->delete();
    config()->set('devdojo.auth.settings.registration_require_email_verification', true);
});

it('sends guests to the login page', function () {
    $this->get('/auth/verify')->assertRedirect(route('login'));
});

it('shows the page to a signed in user', function () {
    $this->actingAs(createUser(['email_verified_at' => null]))
        ->get('/auth/verify')
        ->assertOk();
});

it('resends the verification email without marking the email as verified', function () {
    Notification::fake();
    Event::fake([Verified::class]);
    $user = createUser(['email_verified_at' => null]);

    $this->actingAs($user);
    Livewire::test('auth.verify')->call('resend')->assertDispatched('resent');

    Notification::assertCount(1);
    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('limits how often the verification email can be resent', function () {
    Notification::fake();
    $this->actingAs(createUser(['email_verified_at' => null]));
    $component = Livewire::test('auth.verify');

    foreach (range(1, 8) as $attempt) {
        $component->call('resend');
    }

    Notification::assertCount(6);
});

it('sends a verified user on instead of resending', function () {
    Notification::fake();
    $this->actingAs(createUser(['email_verified_at' => now()]));

    Livewire::test('auth.verify')->call('resend')->assertRedirect('/');

    Notification::assertNothingSent();
});
