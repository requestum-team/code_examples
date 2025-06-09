<?php declare(strict_types=1);

namespace App\Dto\Password;

use Symfony\Component\Validator\Constraints as Assert;


class ForgotPasswordDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
}
