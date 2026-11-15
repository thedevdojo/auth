<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Volt\Volt;

beforeEach(function () {
    config([
        'devdojo.auth.settings.registration_enabled' => true,
        'devdojo.auth.settings.enable_email_registration' => true,
        'devdojo.auth.settings.registration_include_name_field' => true,
        'devdojo.auth.settings.registration_show_password_same_screen' => true,
        'devdojo.auth.settings.registration_include_password_confirmation_field' => false,
        'devdojo.auth.settings.registration_require_email_verification' => false,
    ]);
    Event::fake([Registered::class]);
});

function registrationLengthEmail(int $length): string
{
    return str_repeat('a', 64).'@'.str_repeat('b', 63).'.'.str_repeat('c', $length - 133).'.com';
}

function registerWithLengths($name, $email)
{
    return Volt::test('auth.register')->set('name', $name)->set('email', $email)
        ->set('password', 'registration-password-123')->call('register');
}

function socialRegistrationWithLengths($name, $email)
{
    $socialUser = (new SocialiteUser)->map([
        'id' => 'length-test-id', 'nickname' => 'length-test', 'name' => $name, 'email' => $email, 'avatar' => null,
    ])->setToken('fake-token');
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($socialUser);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

    return test()->get('/auth/google/callback');
}

it('rejects oversized and non-string registration names before account creation', function ($name, string $rule) {
    $count = User::count();
    registerWithLengths($name, 'name-length@example.test')->assertHasErrors(['name' => $rule]);
    expect(User::count())->toBe($count);
    $this->assertGuest();
    Event::assertNotDispatched(Registered::class);
})->with([[str_repeat('n', 192), 'max'], [123, 'string']]);

it('rejects registration emails beyond the database column limit', function () {
    $email = registrationLengthEmail(192);
    registerWithLengths('Length test', $email)->assertHasErrors(['email' => 'max']);
    expect(User::where('email', $email)->exists())->toBeFalse();
    $this->assertGuest();
    Event::assertNotDispatched(Registered::class);
});

it('accepts names and emails at the database column limit', function () {
    $email = registrationLengthEmail(191);
    registerWithLengths(str_repeat('n', 191), $email)->assertHasNoErrors();
    $user = User::where('email', $email)->sole();
    expect(mb_strlen($user->name))->toBe(191)->and(mb_strlen($user->email))->toBe(191);
    $this->assertAuthenticatedAs($user);
    Event::assertDispatched(Registered::class);
});

it('validates the first step before hiding name and email fields', function () {
    config(['devdojo.auth.settings.registration_show_password_same_screen' => false]);
    Volt::test('auth.register')->set('name', str_repeat('n', 192))->set('email', 'step@example.test')
        ->call('register')->assertHasErrors(['name' => 'max'])->assertSet('showPasswordField', false);
    $this->assertGuest();
});

it('continues to ignore the disabled name field', function () {
    config(['devdojo.auth.settings.registration_include_name_field' => false]);
    registerWithLengths(str_repeat('n', 192), 'no-name@example.test')->assertHasNoErrors();
    $this->assertAuthenticated();
});

it('rejects oversized social account attributes before writing either table', function ($name, $email) {
    $users = User::count();
    $providers = DB::table('social_provider_user')->count();
    socialRegistrationWithLengths($name, $email)->assertRedirect(route('auth.login'))->assertSessionHas('error');
    expect(User::count())->toBe($users)->and(DB::table('social_provider_user')->count())->toBe($providers);
    $this->assertGuest();
    Event::assertNotDispatched(Registered::class);
})->with([[str_repeat('n', 192), 'social-length@example.test'], ['Social length', registrationLengthEmail(192)], [123, 'social-type@example.test']]);

it('creates a social account at the database column limits', function () {
    $email = registrationLengthEmail(191);
    socialRegistrationWithLengths(str_repeat('s', 191), $email)->assertRedirect();
    $user = User::where('email', $email)->sole();
    expect(mb_strlen($user->name))->toBe(191)->and($user->socialProviders()->count())->toBe(1);
    $this->assertAuthenticatedAs($user);
    Event::assertDispatched(Registered::class);
});

it('does not reject an already linked social login because its profile name grew', function () {
    $user = User::factory()->create();
    $user->socialProviders()->create(['provider_slug' => 'google', 'provider_user_id' => 'length-test-id', 'token' => 'fake-token']);
    socialRegistrationWithLengths(str_repeat('s', 192), $user->email)->assertRedirect();
    $this->assertAuthenticatedAs($user);
    Event::assertNotDispatched(Registered::class);
});

it('rejects non-string emails before querying uniqueness', function ($email) {
    $count = User::count();
    registerWithLengths('Email type test', $email)->assertHasErrors(['email' => 'string']);
    expect(User::count())->toBe($count);
    $this->assertGuest();
})->with([[123], [['address@example.test']]]);
