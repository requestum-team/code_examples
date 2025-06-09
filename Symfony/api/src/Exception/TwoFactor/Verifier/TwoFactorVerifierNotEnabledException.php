<?php declare(strict_types=1);

namespace App\Exception\TwoFactor\Verifier;

class TwoFactorVerifierNotEnabledException extends AbstractTwoFactorVerifierException
{
    public const MESSAGE = "2FA is not enabled for user";
}
