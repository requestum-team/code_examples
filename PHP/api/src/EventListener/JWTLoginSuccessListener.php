<?php declare(strict_types=1);

// src/EventListener/JWTLoginSuccessListener.php
namespace App\EventListener;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTLoginSuccessListener
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $token = bin2hex(random_bytes(64));
        $expiry = new \DateTimeImmutable('+30 days');

        $refreshToken = new RefreshToken($user, $token, $expiry);
        $refreshToken->markTwoFactorVerified();
        $this->em->persist($refreshToken);
        $this->em->flush();

        $event->setData(array_merge($event->getData(), [
            'refreshToken' => $token,
        ]));
    }
}

