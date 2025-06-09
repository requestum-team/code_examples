<?php declare(strict_types=1);

namespace App\Exception\TwoFactor\Reader;

use JetBrains\PhpStorm\Pure;

class TwoFactorReaderUserNotFoundException extends AbstractTwoFactorReaderException
{
    public const MESSAGE = "User not found";
}
