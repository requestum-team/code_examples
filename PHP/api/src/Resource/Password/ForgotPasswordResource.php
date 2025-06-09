<?php declare(strict_types=1);

namespace App\Resource\Password;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Password\ForgotPasswordDto;
use App\State\Password\ForgotPasswordProcessor;

#[ApiResource(
    shortName: 'ForgotPassword',
    operations: [
        new Post(
            uriTemplate: '/forgot-password',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: ForgotPasswordDto::class,
            output: false,
            processor: ForgotPasswordProcessor::class,
        ),
    ],
    extraProperties: ['tag' => 'Password'],
)]
final class ForgotPasswordResource
{

}
