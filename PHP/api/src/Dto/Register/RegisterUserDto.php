<?php declare(strict_types=1);

namespace App\Dto\Register;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $password;

    #[Assert\NotBlank()]
    public ?string $firstName = null;

    #[Assert\NotBlank()]
    public ?string $lastName = null;
}
