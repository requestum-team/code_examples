<?php

namespace App\State\TwoFactor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\TwoFactor\Verify2FADto;
use App\Exception\TwoFactor\Reader\TwoFactorReaderInvalidTokenException;
use App\Exception\TwoFactor\TwoFactorException;
use App\Service\TwoFactor\Reader\UserReader;
use App\Service\TwoFactor\Verifier\CodeVerifier;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class TwoFactorVerifyProcessor implements ProcessorInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EventDispatcherInterface $eventDispatcher,
        private UserReader $userReader,
        private CodeVerifier $codeVerifier
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (!$data instanceof Verify2FADto) {
            throw new \InvalidArgumentException('Invalid data type. Expected Verify2FADto.');
        }

        try {
            $user = $this->userReader->findUserByToken($data);
            if (!$this->codeVerifier->verify($data, $user)) {
                throw new UnauthorizedHttpException('Bearer', 'Invalid TOTP code');
            }

            $jwtToken = $this->jwtManager->create($user);
            $response = new JsonResponse(['token' => $jwtToken], 200);

            $event = new AuthenticationSuccessEvent(
                ['token' => $jwtToken],
                $user,
                $response
            );
            $this->eventDispatcher->dispatch($event, 'lexik_jwt_authentication.on_authentication_success');

            $response->setData($event->getData());
            return $response;
        } catch (TwoFactorReaderInvalidTokenException|TwoFactorException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (Throwable $e) {
            throw new UnauthorizedHttpException('Bearer', 'Authentication failed');
        }
    }
}
