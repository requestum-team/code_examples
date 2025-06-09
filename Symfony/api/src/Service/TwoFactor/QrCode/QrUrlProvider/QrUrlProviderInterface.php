<?php declare(strict_types=1);

namespace App\Service\TwoFactor\QrCode\QrUrlProvider;

use OTPHP\TOTP;

interface QrUrlProviderInterface
{
    public function provideUrl(TOTP $totp): string;
}
