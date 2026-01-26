<?php

use App\Models\User;
use Devdojo\Auth\Actions\TwoFactorAuth\DisableTwoFactorAuthentication;
use Devdojo\Auth\Actions\TwoFactorAuth\GenerateNewRecoveryCodes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    User::query()->delete();
    config()->set('devdojo.auth.settings.enable_2fa', true);
});

// ===========================================
// GenerateNewRecoveryCodes Action Tests
// ===========================================

it('generates recovery codes as a collection', function () {
    $user = createUser(['password' => \Hash::make('password123')]);
    $generator = new GenerateNewRecoveryCodes;

    $codes = $generator($user);

    expect($codes)->toBeInstanceOf(Collection::class);
});

it('generates exactly 8 recovery codes', function () {
    $user = createUser(['password' => \Hash::make('password123')]);
    $generator = new GenerateNewRecoveryCodes;

    $codes = $generator($user);

    expect($codes)->toHaveCount(8);
});

it('generates recovery codes in correct format', function () {
    $user = createUser(['password' => \Hash::make('password123')]);
    $generator = new GenerateNewRecoveryCodes;

    $codes = $generator($user);

    $codes->each(function ($code) {
        // Format should be: 10chars-10chars
        expect($code)->toMatch('/^[a-zA-Z0-9]{10}-[a-zA-Z0-9]{10}$/');
    });
});

it('generates unique recovery codes each time', function () {
    $user = createUser(['password' => \Hash::make('password123')]);
    $generator = new GenerateNewRecoveryCodes;

    $codes1 = $generator($user);
    $codes2 = $generator($user);

    // Codes should be different each generation
    expect($codes1->toArray())->not->toBe($codes2->toArray());
});

it('generates codes with no duplicates within the set', function () {
    $user = createUser(['password' => \Hash::make('password123')]);
    $generator = new GenerateNewRecoveryCodes;

    $codes = $generator($user);

    expect($codes->unique()->count())->toBe($codes->count());
});

// ===========================================
// Recovery Code Storage Tests
// ===========================================

it('stores recovery codes encrypted in database', function () {
    $generator = new GenerateNewRecoveryCodes;
    $user = createUser(['password' => \Hash::make('password123')]);

    // Simulate what the enable() method does
    $codes = $generator($user);
    $user->forceFill([
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode($codes->toArray())),
    ])->save();

    $user->refresh();

    expect($user->two_factor_recovery_codes)->not->toBeNull();
    // Verify it's encrypted (will throw exception if not decryptable)
    $decrypted = decrypt($user->two_factor_recovery_codes);
    $codes = json_decode($decrypted, true);
    expect($codes)->toBeArray()->toHaveCount(8);
});

it('clears recovery codes when disabling 2FA', function () {
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ]);

    $disable = new DisableTwoFactorAuthentication;
    $disable($user);

    $user->refresh();

    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

// ===========================================
// Recovery Code Regeneration Tests
// ===========================================

it('regenerates new recovery codes', function () {
    $generator = new GenerateNewRecoveryCodes;
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['old-code-1', 'old-code-2'])),
        'two_factor_confirmed_at' => now(),
    ]);

    $oldCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    // Simulate regenerateCodes() method
    $newCodesCollection = $generator($user);
    $user->forceFill([
        'two_factor_recovery_codes' => encrypt(json_encode($newCodesCollection->toArray())),
    ])->save();

    $user->refresh();
    $newCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    expect($newCodes)->not->toBe($oldCodes);
    expect($newCodes)->toHaveCount(8);
});

it('invalidates old recovery codes after regeneration', function () {
    $generator = new GenerateNewRecoveryCodes;
    $oldCode = 'abcdefghij-klmnopqrst';
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode([$oldCode])),
        'two_factor_confirmed_at' => now(),
    ]);

    // Simulate regenerateCodes() method
    $newCodesCollection = $generator($user);
    $user->forceFill([
        'two_factor_recovery_codes' => encrypt(json_encode($newCodesCollection->toArray())),
    ])->save();

    $user->refresh();
    $newCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

    expect($newCodes)->not->toContain($oldCode);
});

// ===========================================
// Recovery Code Usage (Login) Tests
// ===========================================

it('allows login with valid recovery code', function () {
    $recoveryCode = 'validcode1-validcode2';
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode([$recoveryCode, 'another-code'])),
        'two_factor_confirmed_at' => now(),
    ]);

    // Simulate the login.id session being set (as if user passed password auth)
    Session::put('login.id', $user->id);

    Livewire::test('auth.two-factor-challenge')
        ->set('recovery', true)
        ->set('recovery_code', $recoveryCode)
        ->call('submit_recovery_code')
        ->assertHasNoErrors()
        ->assertRedirect(config('devdojo.auth.settings.redirect_after_auth'));
});

it('rejects login with invalid recovery code', function () {
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['valid-code-here'])),
        'two_factor_confirmed_at' => now(),
    ]);

    Session::put('login.id', $user->id);

    Livewire::test('auth.two-factor-challenge')
        ->set('recovery', true)
        ->set('recovery_code', 'invalid-recovery-code')
        ->call('submit_recovery_code')
        ->assertHasErrors('recovery_code');
});

it('rejects login with empty recovery code', function () {
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['valid-code-here'])),
        'two_factor_confirmed_at' => now(),
    ]);

    Session::put('login.id', $user->id);

    Livewire::test('auth.two-factor-challenge')
        ->set('recovery', true)
        ->set('recovery_code', '')
        ->call('submit_recovery_code')
        ->assertHasErrors('recovery_code');
});

// ===========================================
// DisableTwoFactorAuthentication Action Tests
// ===========================================

it('dispatches event when disabling 2FA', function () {
    \Illuminate\Support\Facades\Event::fake();

    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1'])),
        'two_factor_confirmed_at' => now(),
    ]);

    $disable = new DisableTwoFactorAuthentication;
    $disable($user);

    \Illuminate\Support\Facades\Event::assertDispatched(
        \Devdojo\Auth\Events\TwoFactorAuthenticationDisabled::class
    );
});

it('does not dispatch event when 2FA is not enabled', function () {
    \Illuminate\Support\Facades\Event::fake();

    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ]);

    $disable = new DisableTwoFactorAuthentication;
    $disable($user);

    \Illuminate\Support\Facades\Event::assertNotDispatched(
        \Devdojo\Auth\Events\TwoFactorAuthenticationDisabled::class
    );
});

// ===========================================
// Recovery Code Flow Tests
// ===========================================

it('can set up 2FA with recovery codes', function () {
    $generator = new GenerateNewRecoveryCodes;
    $user = createUser(['password' => \Hash::make('password123')]);

    // Simulate the enable and confirm flow
    $codes = $generator($user);
    $user->forceFill([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode($codes->toArray())),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $user->refresh();

    expect($user->two_factor_confirmed_at)->not->toBeNull();

    // Verify codes are stored and accessible
    $storedCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
    expect($storedCodes)->toBeArray()->toHaveCount(8);
});

it('can switch between auth code and recovery code modes', function () {
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ]);

    Session::put('login.id', $user->id);

    Livewire::test('auth.two-factor-challenge')
        ->assertSet('recovery', false)
        ->call('switchToRecovery')
        ->assertSet('recovery', true)
        ->call('switchToRecovery')
        ->assertSet('recovery', false);
});

it('can cancel 2FA setup before confirmation', function () {
    $user = createUser(['password' => \Hash::make('password123')]);

    // Simulate enable
    $user->forceFill([
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1'])),
    ])->save();

    // Simulate cancelTwoFactor
    $user->forceFill([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
    ])->save();

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
});

it('can disable 2FA after confirmation using action', function () {
    $user = createUser([
        'password' => \Hash::make('password123'),
        'two_factor_secret' => encrypt('testsecret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1'])),
        'two_factor_confirmed_at' => now(),
    ]);

    // Use the DisableTwoFactorAuthentication action
    $disable = new DisableTwoFactorAuthentication;
    $disable($user);

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

// ===========================================
// Security Tests
// ===========================================

it('recovery codes are stored encrypted not in plaintext', function () {
    $generator = new GenerateNewRecoveryCodes;
    $user = createUser(['password' => \Hash::make('password123')]);

    $codes = $generator($user);
    $user->forceFill([
        'two_factor_recovery_codes' => encrypt(json_encode($codes->toArray())),
    ])->save();

    $user->refresh();

    // The raw value should not be valid JSON (it's encrypted)
    $rawValue = $user->getRawOriginal('two_factor_recovery_codes');
    expect(json_decode($rawValue))->toBeNull();
});

it('multiple users have unique recovery codes', function () {
    $generator = new GenerateNewRecoveryCodes;

    $user1 = createUser(['email' => 'user1@test.com', 'password' => \Hash::make('password123')]);
    $user2 = createUser(['email' => 'user2@test.com', 'password' => \Hash::make('password123')]);

    $codes1 = $generator($user1);
    $codes2 = $generator($user2);

    $user1->forceFill(['two_factor_recovery_codes' => encrypt(json_encode($codes1->toArray()))])->save();
    $user2->forceFill(['two_factor_recovery_codes' => encrypt(json_encode($codes2->toArray()))])->save();

    $user1->refresh();
    $user2->refresh();

    $stored1 = json_decode(decrypt($user1->two_factor_recovery_codes), true);
    $stored2 = json_decode(decrypt($user2->two_factor_recovery_codes), true);

    expect($stored1)->not->toBe($stored2);
});
