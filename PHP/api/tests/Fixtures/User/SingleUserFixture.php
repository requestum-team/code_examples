<?php declare(strict_types=1);

namespace App\Tests\Fixtures\User;

use App\Entity\User;
use App\Tests\Fixtures\TestFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SingleUserFixture implements TestFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword($this->hasher->hashPassword($user, 'user123'));
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $manager->persist($user);

        $manager->flush();
    }
}
