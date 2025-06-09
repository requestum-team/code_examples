<?php declare(strict_types=1);

namespace App\Exception\TwoFactor\Reader;

use JetBrains\PhpStorm\Pure;

class TwoFactorReaderInvalidTokenException extends AbstractTwoFactorReaderException
{
    public const MESSAGE = "Invalid or expired token";
}
