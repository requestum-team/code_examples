<?php declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherListener {
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(PrePersistEventArgs $event): void {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodePassword($entity);
    }

    public function preUpdate(PreUpdateEventArgs $event): void {
        $entity = $event->getObject();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodePassword($entity);
    }

    private function encodePassword(User $user): void {
        if ($user->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
        }
    }
}
