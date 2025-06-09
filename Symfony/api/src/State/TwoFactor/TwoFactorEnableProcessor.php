<?php declare(strict_types=1);

namespace App\State\TwoFactor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\TwoFactor\Generator\TwoFactorGenerator;
use App\Service\TwoFactor\QrCode\QrCodeUrlFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class TwoFactorEnableProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private TwoFactorGenerator $twoFactorGenerator,
        private QrCodeUrlFactory $qrCodeUrlFactory,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User is unauthorized.'], 400);
        }

        $totp = $this->twoFactorGenerator->generate($user);
        $url = $this->qrCodeUrlFactory->createQrCodeUrl($totp);

        return new JsonResponse([
            'qrUrl' => $url,
            'secret' => $user->getTotpSecret(),
        ]);
    }
}

