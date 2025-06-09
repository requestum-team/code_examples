<?php declare(strict_types=1);

namespace App\Service\TwoFactor\Verifier;

use App\Dto\TwoFactor\Verify2FADto;
use App\Entity\User;
use App\Exception\TwoFactor\Verifier\TwoFactorVerifierNotEnabledException;
use OTPHP\TOTP;

class CodeVerifier
{
    public function verify(Verify2FADto $dto, User $user): bool
    {
        if (!$user->isTotpAuthenticationEnabled()) {
            throw new TwoFactorVerifierNotEnabledException();
        }

        // Verify the TOTP code
        $totp = TOTP::create($user->getTotpSecret());

        return $totp->verify($dto->totpCode);
    }
}
