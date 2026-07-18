<?php

use Devdojo\Auth\Enums\CodeCheckResult;
use Devdojo\Auth\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

// The package's own User model carries the trait; the consuming app's
// App\Models\User (absent in the package CI harness) is not assumed.
function emailCodeUser(): User
{
    return User::forceCreate([
        'name' => 'Code Tester',
        'email' => uniqid('code-user-', true).'@example.com',
        'password' => bcrypt('secret-password'),
    ]);
}

it('issues a single active code and verifies with it', function () {
    Event::fake([Verified::class]);
    $user = emailCodeUser();

    $code = $user->issueEmailVerificationCode();

    expect($user->emailVerificationCode()->count())->toBe(1)
        ->and($user->verifyEmailWithCode($code))->toBe(CodeCheckResult::Verified)
        ->and($user->fresh()->email_verified_at)->not->toBeNull()
        ->and($user->emailVerificationCode()->exists())->toBeFalse();

    Event::assertDispatched(Verified::class);
});

// The two-emails-in-one-minute regression: a slow first delivery plus a
// resend must not strand a dead code in the inbox the user is reading.
it('re-issues the same code while it is still valid, so every email works', function () {
    $user = emailCodeUser();

    $first = $user->issueEmailVerificationCode();
    $resent = $user->issueEmailVerificationCode();

    expect($resent)->toBe($first)
        ->and($user->emailVerificationCode()->count())->toBe(1)
        ->and($user->verifyEmailWithCode($first))->toBe(CodeCheckResult::Verified);
});

it('restarts expiry and the resend cooldown when the code is re-issued', function () {
    $user = emailCodeUser();

    $code = $user->issueEmailVerificationCode();

    $this->travel(14)->minutes();
    expect($user->verificationCodeCooldownRemaining())->toBe(0);

    // Re-sending near the end of the window keeps the emailed copy honest:
    // the code is valid for the full expiry again, and the cooldown restarts.
    expect($user->issueEmailVerificationCode())->toBe($code)
        ->and($user->verificationCodeCooldownRemaining())->toBeGreaterThan(50);

    $this->travel(14)->minutes();
    expect($user->verifyEmailWithCode($code))->toBe(CodeCheckResult::Verified);
    $this->travelBack();
});

it('rotates a fresh code once the old one expires', function () {
    $user = emailCodeUser();

    $first = $user->issueEmailVerificationCode();

    $this->travel(16)->minutes();
    expect($user->verifyEmailWithCode($first))->toBe(CodeCheckResult::Expired);

    $second = $user->issueEmailVerificationCode();
    expect($second)->not->toBe($first)
        ->and($user->verifyEmailWithCode($second))->toBe(CodeCheckResult::Verified);
    $this->travelBack();
});

it('rejects wrong codes and locks after the attempt cap', function () {
    $user = emailCodeUser();
    $code = $user->issueEmailVerificationCode();

    for ($i = 1; $i <= 4; $i++) {
        expect($user->verifyEmailWithCode('000000'))->toBe(CodeCheckResult::Invalid);
    }

    expect($user->verifyEmailWithCode('000000'))->toBe(CodeCheckResult::TooManyAttempts)
        ->and($user->verifyEmailWithCode($code))->toBe(CodeCheckResult::TooManyAttempts)
        ->and($user->fresh()->email_verified_at)->toBeNull();
});

it('rotates a fresh code after an attempt lockout instead of reusing the burned one', function () {
    $user = emailCodeUser();
    $first = $user->issueEmailVerificationCode();

    for ($i = 1; $i <= 5; $i++) {
        $user->verifyEmailWithCode('000000');
    }

    $second = $user->issueEmailVerificationCode();

    expect($second)->not->toBe($first)
        ->and($user->verifyEmailWithCode($second))->toBe(CodeCheckResult::Verified);
});

it('treats a missing code as expired and reports the resend cooldown', function () {
    $user = emailCodeUser();

    expect($user->verifyEmailWithCode('123456'))->toBe(CodeCheckResult::Expired)
        ->and($user->verificationCodeCooldownRemaining())->toBe(0);

    $user->issueEmailVerificationCode();
    expect($user->verificationCodeCooldownRemaining())->toBeGreaterThan(50);
});

it('rotates the code when explicitly invalidated (email change)', function () {
    $user = emailCodeUser();

    $first = $user->issueEmailVerificationCode();

    $user->invalidateEmailVerificationCodes();

    expect($user->verifyEmailWithCode($first))->toBe(CodeCheckResult::Expired)
        ->and($user->issueEmailVerificationCode())->not->toBe($first);
});

it('treats an unreadable legacy hashed code as expired so a resend rotates it', function () {
    $user = emailCodeUser();

    $user->emailVerificationCode()->create([
        'code' => password_hash('123456', PASSWORD_BCRYPT), // pre-encryption row
        'expires_at' => now()->addMinutes(15),
    ]);

    expect($user->verifyEmailWithCode('123456'))->toBe(CodeCheckResult::Expired);

    $fresh = $user->issueEmailVerificationCode();
    expect($user->verifyEmailWithCode($fresh))->toBe(CodeCheckResult::Verified);
});
