<?php declare(strict_types=1);

namespace App\Dto\Password;

use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordDto
{
    #[Assert\NotBlank]
    public string $token;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $newPassword;
}
