<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;

class TwoFactorAuthenticationController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    public function confirmAuthenticationCode(Request $request)
    {
        $user = $request->user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            // Handle successful authentication
        } else {
            // Handle failed authentication
        }
    }

    public function confirmRecoveryCode(Request $request)
    {
        $user = $request->user();
        $recoveryCodes = $user->two_factor_recovery_codes;
        $valid = in_array($request->code, $recoveryCodes);

        if ($valid) {
            // Handle successful recovery code confirmation
        } else {
            // Handle failed recovery code confirmation
        }
    }
}
