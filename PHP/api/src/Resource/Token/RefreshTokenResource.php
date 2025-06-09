<?php declare(strict_types=1);

namespace App\Resource\Token;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Token\RefreshTokenDto;
use App\State\Token\RefreshTokenProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/token/refresh',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: RefreshTokenDto::class,
            output: false,
            read: false,
            validate: true,
            processor: RefreshTokenProcessor::class,
        )
    ],
    extraProperties: ['tag' => 'Auth'],
)]
final class RefreshTokenResource
{

}
