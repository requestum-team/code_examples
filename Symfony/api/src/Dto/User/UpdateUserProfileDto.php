<?php declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserProfileDto
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public ?string $firstName = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public ?string $lastName = null;
}
