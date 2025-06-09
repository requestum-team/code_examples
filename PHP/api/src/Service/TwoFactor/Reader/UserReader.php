<?php declare(strict_types=1);

namespace App\Service\TwoFactor\Reader;

use App\Dto\TwoFactor\Verify2FADto;
use App\Entity\User;
use App\Exception\TwoFactor\Reader\TwoFactorReaderInvalidTokenException;
use App\Exception\TwoFactor\Reader\TwoFactorReaderUserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserReader
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function findUserByToken(Verify2FADto $dto): User
    {
        $payload = $this->jwtManager->parse($dto->token);
        $userId = $payload['user_id'] ?? null;
        $is2faRequired = $payload['2fa_required'] ?? false;

        if (!$userId || !$is2faRequired) {
            throw new TwoFactorReaderInvalidTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new TwoFactorReaderUserNotFoundException();
        }

        return $user;
    }
}
