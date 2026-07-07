<?php

namespace Devdojo\Auth\Traits;

use Devdojo\Auth\Enums\CodeCheckResult;
use Devdojo\Auth\Models\EmailVerificationCode;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

/**
 * Code-based email verification: a single active hashed 6-digit code per
 * user, with expiry, attempt caps, and a resend cooldown — the inline
 * companion to Laravel's signed verification link.
 */
trait HasEmailVerificationCodes
{
    public function emailVerificationCode(): HasOne
    {
        return $this->hasOne(EmailVerificationCode::class, 'user_id');
    }

    public function issueEmailVerificationCode(): string
    {
        $code = (string) random_int(100000, 999999);

        $this->emailVerificationCode()->delete();
        $this->emailVerificationCode()->create([
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('devdojo.auth.settings.verification_code_expires_in', 15)),
        ]);
        $this->unsetRelation('emailVerificationCode');

        return $code;
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

        if (! Hash::check($code, $record->code_hash)) {
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
}
