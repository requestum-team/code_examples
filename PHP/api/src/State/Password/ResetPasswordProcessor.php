<?php declare(strict_types=1);

namespace App\State\Password;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Password\ResetPasswordDto;
use App\Entity\PasswordResetToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResetPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof ResetPasswordDto) {
            throw new \InvalidArgumentException('Invalid data.');
        }

        $reset = $this->em->getRepository(PasswordResetToken::class)->findOneBy([
            'token' => $data->token,
            'used' => false,
        ]);

        if (!$reset || $reset->getExpiresAt() < new \DateTimeImmutable()) {
            throw new BadRequestHttpException('Invalid or expired token.');
        }

        $user = $reset->getUser();
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->newPassword));

        $reset->setUsed(true);

        $this->em->flush();

        return ['message' => 'Password reset successful.'];
    }
}

