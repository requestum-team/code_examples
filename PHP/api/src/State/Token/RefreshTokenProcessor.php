<?php declare(strict_types=1);

namespace App\State\Token;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Token\RefreshTokenDto;
use App\Entity\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RefreshTokenProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        assert($data instanceof RefreshTokenDto);

        $token = $data->refresh_token;
        $repository = $this->entityManager->getRepository(RefreshToken::class);

        $stored = $repository->find($token);
        if (!$stored || $stored->getExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $stored->getUser();
        $accessToken = $this->jwtManager->create($user);

        return new JsonResponse([
            'token' => $accessToken,
        ]);
    }
}

