<?php declare(strict_types=1);

namespace App\Tests\Fixtures\User;

use App\Entity\Image;
use App\Entity\User;
use App\Tests\Fixtures\TestFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserWithImagesFixture implements TestFixtureInterface
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

        $path = __DIR__ . '/../../Resources/images/user.jpg';
        $uploadedFile = new File($path);

        $image = new Image();
        $image->setFile($uploadedFile);
        $image->setOwner($user);
        $image->setFilename($uploadedFile->getFilename());
        $manager->persist($image);

        $manager->flush();
    }
}
