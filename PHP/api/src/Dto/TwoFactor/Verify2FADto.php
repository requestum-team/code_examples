<?php declare(strict_types=1);

namespace App\Dto\TwoFactor;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class Verify2FADto
{
    #[Assert\NotBlank]
    #[Groups(['write'])]
    #[SerializedName('token')]
    public string $token;

    #[Assert\NotBlank]
    #[Groups(['write'])]
    #[SerializedName('totpCode')]
    public string $totpCode;
}
