<?php

use App\Models\User;
use Devdojo\Auth\Enums\CodeCheckResult;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

it('issues a hashed single-active code and verifies with it', function () {
    Event::fake([Verified::class]);
    $user = User::factory()->create(['email_verified_at' => null]);

    $first = $user->issueEmailVerificationCode();
    $second = $user->issueEmailVerificationCode(); // replaces the first

    expect($user->emailVerificationCode()->count())->toBe(1)
        ->and($user->verifyEmailWithCode($second))->toBe(CodeCheckResult::Verified)
        ->and($user->fresh()->email_verified_at)->not->toBeNull()
        ->and($user->emailVerificationCode()->exists())->toBeFalse();

    Event::assertDispatched(Verified::class);
});

it('treats a replaced or missing code as expired', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $first = $user->issueEmailVerificationCode();
    $user->issueEmailVerificationCode();

    expect($user->verifyEmailWithCode($first))->toBe(CodeCheckResult::Invalid);

    $user->emailVerificationCode()->delete();
    expect($user->verifyEmailWithCode('123456'))->toBe(CodeCheckResult::Expired);
});

it('rejects wrong codes and locks after the attempt cap', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $code = $user->issueEmailVerificationCode();

    for ($i = 1; $i <= 4; $i++) {
        expect($user->verifyEmailWithCode('000000'))->toBe(CodeCheckResult::Invalid);
    }

    expect($user->verifyEmailWithCode('000000'))->toBe(CodeCheckResult::TooManyAttempts)
        ->and($user->verifyEmailWithCode($code))->toBe(CodeCheckResult::TooManyAttempts)
        ->and($user->fresh()->email_verified_at)->toBeNull();
});

it('expires codes and reports the resend cooldown', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $code = $user->issueEmailVerificationCode();

    expect($user->verificationCodeCooldownRemaining())->toBeGreaterThan(50);

    $this->travel(16)->minutes();
    expect($user->verifyEmailWithCode($code))->toBe(CodeCheckResult::Expired)
        ->and($user->verificationCodeCooldownRemaining())->toBe(0);
    $this->travelBack();
});
