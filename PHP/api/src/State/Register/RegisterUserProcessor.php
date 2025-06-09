<?php declare(strict_types=1);

namespace App\State\Register;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Register\RegisterUserDto;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        try {
            assert($data instanceof RegisterUserDto);

            $existing = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $data->email]);

            if ($existing !== null) {
                throw new BadRequestException('User exist');
            }

            $user = new User();
            $user->setEmail($data->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
            $user->setFirstName($data->firstName);
            $user->setLastName($data->lastName);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        } catch (UniqueConstraintViolationException $e) {
            throw new BadRequestException('User exist');
        }
    }
}
