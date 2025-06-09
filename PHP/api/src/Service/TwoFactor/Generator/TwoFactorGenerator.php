<?php declare(strict_types=1);

namespace App\Service\TwoFactor\Generator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;

class TwoFactorGenerator
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function generate(User $user): TOTP
    {
        $totp = TOTP::create();
        $totp->setLabel($user->getEmail());
        $totp->setIssuer('MyApp');
        $user->setTotpSecret($totp->getSecret());
        $user->enable2FA();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $totp;
    }
}
