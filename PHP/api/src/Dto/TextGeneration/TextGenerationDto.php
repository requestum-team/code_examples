<?php declare(strict_types=1);

namespace App\Dto\TextGeneration;

use Symfony\Component\Validator\Constraints as Assert;

class TextGenerationDto
{
    #[Assert\NotBlank]
    public string $prompt;
}
