<?php

namespace Devdojo\Auth\Actions\TwoFactorAuth;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Devdojo\Auth\Models\User;
use PragmaRX\Google2FA\Google2FA;

class GenerateQrCodeAndSecretKey
{
    public string $companyName;

    /**
     * Generate new recovery codes for the user.
     *
     * @return array{string, string}
     */
    public function __invoke($user): array
    {

        $google2fa = new Google2FA;
        $secret_key = $google2fa->generateSecretKey();

        $this->companyName = 'Auth';
        if (is_string(config('app.name'))) {
            $this->companyName = config('app.name');
        }

        $g2faUrl = $google2fa->getQRCodeUrl(
            $this->companyName,
            (string) $user->email,
            $secret_key
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(800),
                new ImagickImageBackEnd
            )
        );

        $qrcode_image = base64_encode($writer->writeString($g2faUrl));

        return [$qrcode_image, $secret_key];

    }
}
