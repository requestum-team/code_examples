<?php declare(strict_types=1);

namespace App\State\Image;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Image\UploadImageDto;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Image
    {
        $request = $this->requestStack->getCurrentRequest();
        $file = $request->files->get('file');

        $dto = new UploadImageDto();
        $dto->file = $file;

        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            throw new UnprocessableEntityHttpException((string) $violations);
        }

        $user = $this->security->getUser();

        $image = new Image();
        $image->setFile($file);
        $image->setOwner($user);

        $this->em->persist($image);
        $this->em->flush();

        return $image;
    }
}
