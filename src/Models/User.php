<?php

namespace Devdojo\Auth\Models;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Devdojo\Auth\Notifications\VerifyEmailWithCode;
use Devdojo\Auth\Traits\HasEmailVerificationCodes;
use Devdojo\Auth\Traits\HasSocialProviders;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use PragmaRX\Google2FA\Google2FA;

/**
 * Class User
 *
 * @property string|null $email
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property HasMany $socialProviders
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasEmailVerificationCodes, HasSocialProviders, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'email_verified_at',
    ];

    public function hasVerifiedEmail()
    {
        if (! config('devdojo.auth.settings.registration_require_email_verification')) {
            return true;
        }

        return $this->email_verified_at !== null;
    }

    /**
     * Verification emails carry both the 6-digit code and the signed link.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailWithCode($this->issueEmailVerificationCode()));
    }

    public function twoFactorQrCodeSvg()
    {
        return '';
    }

    /**
     * Enable 2FA for the user.
     */
    public function enableTwoFactorAuthentication()
    {
        $google2fa = app(Google2FA::class);
        $this->two_factor_secret = $google2fa->generateSecretKey();
        $this->save();
    }

    /**
     * Disable 2FA for the user.
     */
    public function disableTwoFactorAuthentication()
    {
        $this->two_factor_secret = null;
        $this->save();
    }

    /**
     * Generate a QR code for 2FA.
     *
     * @return string
     */
    public function generateTwoFactorQrCodeSvg()
    {
        $google2fa = app(Google2FA::class);
        $companyName = config('app.name');
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            $companyName,
            $this->email,
            $this->two_factor_secret
        );

        return SvgImageBackEnd::class;
    }
}
