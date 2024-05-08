<?php

namespace Devdojo\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Devdojo\Auth\Traits\HasSocialProviders;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasSocialProviders;

    protected $fillable = [
        'name', 'email', 'password', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    public function hasVerifiedEmail()
    {
        if (!config('devdojo.auth.settings.registration_require_email_verification')) {
            return true;
        }

        return $this->email_verified_at !== null;
    }

    public function twoFactorQrCodeSvg(){
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

        return \BaconQrCode\Renderer\Image\SvgImageBackEnd::class;
    }
}
