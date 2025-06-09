<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private const USERS_DATA = [
        [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'user1@example.com',
            'password' => 'password1',
        ],
        [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'user2@example.com',
            'password' => 'password2',
        ],
        [
            'firstName' => 'Jack',
            'lastName' => 'Doe',
            'email' => 'user3@example.com',
            'password' => 'password3',
        ],
    ];

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS_DATA as $userData) {
            $user = new User();
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            $manager->persist($user);
        }

        // Save everything to the database
        $manager->flush();
    }
}
