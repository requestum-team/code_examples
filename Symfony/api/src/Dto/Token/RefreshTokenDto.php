<?php declare(strict_types=1);

namespace App\Dto\Token;

use Symfony\Component\Validator\Constraints as Assert;

class RefreshTokenDto
{
    #[Assert\NotBlank]
    public string $refresh_token;
}

