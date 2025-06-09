<?php declare(strict_types=1);

namespace App\Resource\TextGeneration;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\TextGeneration\TextGenerationDto;
use App\Dto\TextGeneration\TextGenerationResponseDto;
use App\State\TextGeneration\TextGenerationProcessor;

#[ApiResource(
    shortName: 'TextGeneration',
    operations: [
        new Post(
            uriTemplate: '/ask',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: TextGenerationDto::class,
            output: TextGenerationResponseDto::class,
            processor: TextGenerationProcessor::class,
        )
    ],
)]
final class TextGenerationResource
{

}
