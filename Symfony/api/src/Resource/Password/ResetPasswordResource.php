<?php declare(strict_types=1);

namespace App\Resource\Password;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Password\ResetPasswordDto;
use App\State\Password\ResetPasswordProcessor;

#[ApiResource(
    shortName: 'ResetPassword',
    operations: [
        new Post(
            uriTemplate: '/reset-password',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: ResetPasswordDto::class,
            output: false,
            processor: ResetPasswordProcessor::class,
        ),
    ],
    extraProperties: ['tag' => 'Password'],
)]
final class ResetPasswordResource
{

}
