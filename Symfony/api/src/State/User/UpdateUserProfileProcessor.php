<?php declare(strict_types=1);

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\User\UpdateUserProfileDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;

class UpdateUserProfileProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $user = $this->security->getUser();
        assert($user instanceof User);
        assert($data instanceof UpdateUserProfileDto);

        if ($data->firstName !== null) {
            $user->setFirstName($data->firstName);
        }

        if ($data->lastName !== null) {
            $user->setLastName($data->lastName);
        }

        $this->em->flush();

        return $user;
    }
}
