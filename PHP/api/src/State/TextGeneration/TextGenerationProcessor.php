<?php declare(strict_types=1);

namespace App\State\TextGeneration;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\TextGeneration\TextGenerationDto;
use App\Dto\TextGeneration\TextGenerationResponseDto;
use App\Service\OpenApi\GroqService;
use function assert;

class TextGenerationProcessor implements ProcessorInterface
{
    public function __construct(private GroqService $groq)
    {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): TextGenerationResponseDto
    {
        assert($data instanceof TextGenerationDto);

        $reply = $this->groq->ask($data->prompt);

        return new TextGenerationResponseDto($reply);
    }
}

