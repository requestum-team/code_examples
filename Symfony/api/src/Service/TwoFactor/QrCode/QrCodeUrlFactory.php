<?php declare(strict_types=1);

namespace App\Service\TwoFactor\QrCode;

use App\Service\TwoFactor\QrCode\QrUrlProvider\QrUrlProviderInterface;
use OTPHP\TOTP;

class QrCodeUrlFactory
{
    public function __construct(private QrUrlProviderInterface $qrUrlProvider)
    {
    }

    /**
     * @inheritDoc
     */
    public function createQrCodeUrl(TOTP $totp): string
    {
        return $this->qrUrlProvider->provideUrl($totp);
    }
}
