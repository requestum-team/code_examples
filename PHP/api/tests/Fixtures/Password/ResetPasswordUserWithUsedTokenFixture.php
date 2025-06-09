<?php declare(strict_types=1);

namespace App\Tests\Fixtures\Password;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Tests\Fixtures\TestFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ResetPasswordUserWithUsedTokenFixture implements TestFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword($this->hasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        $token = new PasswordResetToken();
        $token->setToken('test-reset-token');
        $token->setUser($user);
        $token->setExpiresAt(new \DateTimeImmutable('+1 hour'));
        $token->setUsed(true);
        $manager->persist($token);

        $manager->flush();
    }
}
