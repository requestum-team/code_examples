<?php declare(strict_types=1);

namespace App\Dto\TextGeneration;

use Symfony\Component\Validator\Constraints as Assert;

class TextGenerationResponseDto
{
    public function __construct(public string $reply) {}
}
