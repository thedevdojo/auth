<?php

namespace Devdojo\Auth\Actions\TwoFactorAuth;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function __invoke($user)
    {
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return $this->generate();
            })->all())),
        ])->save();
    }

    public function generate(){
        return Str::random(10).'-'.Str::random(10);
    }
}
