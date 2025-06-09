<?php declare(strict_types=1);

namespace App\Service\TwoFactor\QrCode\QrUrlProvider;

use OTPHP\TOTP;

class QrServerUrlProvider implements QrUrlProviderInterface
{
    private const QR_SERVER_URL_PREFIX = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=';

    public function provideUrl(TOTP $totp): string
    {
        // Create QR code URL
        $otpUri = $totp->getProvisioningUri();

        // Convert to base64 QR image
        return self::QR_SERVER_URL_PREFIX . rawurlencode($otpUri);
    }
}
