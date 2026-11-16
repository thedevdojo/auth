<?php

namespace Devdojo\Auth\Traits;

use Devdojo\Auth\Enums\CodeCheckResult;
use Devdojo\Auth\Models\EmailVerificationCode;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;

/**
 * Code-based email verification: a single active 6-digit code per user,
 * with expiry, attempt caps, and a resend cooldown — the inline companion
 * to Laravel's signed verification link.
 *
 * Re-issuing while a code is still valid returns the SAME code (stored
 * encrypted, not hashed, precisely so it can be re-sent). Rotating on
 * every send looks harmless but breaks real inboxes: the first email is
 * slow, the user hits resend, two emails land within the same minute, and
 * the one they open first carries a dead code. Expiry and the resend
 * cooldown restart on each re-issue; a fresh code is only minted when the
 * current one is missing, expired, or attempt-locked.
 */
trait HasEmailVerificationCodes
{
    public function emailVerificationCode(): HasOne
    {
        return $this->hasOne(EmailVerificationCode::class, 'user_id');
    }

    public function issueEmailVerificationCode(): string
    {
        if (! is_null($code = $this->reusableVerificationCode())) {
            return $code;
        }

        $code = (string) random_int(100000, 999999);

        $this->emailVerificationCode()->delete();
        $this->emailVerificationCode()->create([
            'code' => Crypt::encryptString($code),
            'expires_at' => now()->addMinutes($this->verificationCodeExpiryMinutes()),
        ]);
        $this->unsetRelation('emailVerificationCode');

        return $code;
    }

    /**
     * Kill the active code outright — the next issue mints a fresh one.
     * Required when the destination changes (e.g. the user corrects a typo'd
     * email): the copy already delivered to the old address must not be able
     * to verify the new one.
     */
    public function invalidateEmailVerificationCodes(): void
    {
        $this->emailVerificationCode()->delete();
        $this->unsetRelation('emailVerificationCode');
    }

    public function verifyEmailWithCode(string $code): CodeCheckResult
    {
        $record = $this->emailVerificationCode()->first();

        if (! $record || $record->expires_at->isPast()) {
            return CodeCheckResult::Expired;
        }

        $max = (int) config('devdojo.auth.settings.verification_code_max_attempts', 5);

        if ($record->attempts >= $max) {
            return CodeCheckResult::TooManyAttempts;
        }

        $stored = $this->decryptVerificationCode($record);

        if (is_null($stored)) {
            // A pre-encryption (hashed) row we can no longer read — expire it
            // so the resend path rotates in a fresh, readable code.
            return CodeCheckResult::Expired;
        }

        if (! hash_equals($stored, $code)) {
            $record->increment('attempts');

            return $record->attempts >= $max ? CodeCheckResult::TooManyAttempts : CodeCheckResult::Invalid;
        }

        $record->delete();
        $this->unsetRelation('emailVerificationCode');

        // Real column state, not hasVerifiedEmail() — the package's own
        // override reports everyone verified while the require flag is off.
        if (is_null($this->email_verified_at)) {
            $this->markEmailAsVerified();
            event(new Verified($this));
        }

        return CodeCheckResult::Verified;
    }

    public function verificationCodeCooldownRemaining(): int
    {
        $issuedAt = $this->emailVerificationCode()->first()?->created_at;

        if (! $issuedAt) {
            return 0;
        }

        $cooldown = (int) config('devdojo.auth.settings.verification_code_resend_cooldown', 60);

        return max(0, $cooldown - (int) abs(now()->diffInSeconds($issuedAt)));
    }

    /**
     * The current code, when it can still be honored — un-expired, under the
     * attempt cap, and readable. Re-issuing restarts both the expiry window
     * (the email's "expires in N minutes" stays true) and the resend
     * cooldown (created_at doubles as the last-sent timestamp).
     */
    protected function reusableVerificationCode(): ?string
    {
        $record = $this->emailVerificationCode()->first();

        if (! $record || $record->expires_at->isPast()) {
            return null;
        }

        if ($record->attempts >= (int) config('devdojo.auth.settings.verification_code_max_attempts', 5)) {
            return null;
        }

        $code = $this->decryptVerificationCode($record);

        if (is_null($code)) {
            return null;
        }

        $record->forceFill([
            'created_at' => now(),
            'expires_at' => now()->addMinutes($this->verificationCodeExpiryMinutes()),
        ])->save();
        $this->unsetRelation('emailVerificationCode');

        return $code;
    }

    protected function decryptVerificationCode(EmailVerificationCode $record): ?string
    {
        try {
            return Crypt::decryptString($record->code);
        } catch (DecryptException) {
            return null;
        }
    }

    protected function verificationCodeExpiryMinutes(): int
    {
        return (int) config('devdojo.auth.settings.verification_code_expires_in', 15);
    }
}
