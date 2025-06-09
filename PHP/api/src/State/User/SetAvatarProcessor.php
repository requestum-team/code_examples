<?php declare(strict_types=1);

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\User\SetAvatarDto;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function assert;

class SetAvatarProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        assert($data instanceof SetAvatarDto);

        $user = $this->security->getUser();
        assert($user instanceof User);

        $image = $this->em->getRepository(Image::class)->find($data->imageId);

        if (!$image || $image->getOwner()->getId() !== $user->getId()) {
            throw new BadRequestHttpException('Image not found or not owned by user.');
        }

        $user->setAvatar($image);
        $this->em->flush();

        return $user;
    }
}
