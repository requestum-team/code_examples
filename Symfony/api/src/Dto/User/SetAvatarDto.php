<?php declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class SetAvatarDto
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $imageId;
}

