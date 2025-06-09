<?php declare(strict_types=1);

namespace App\Resource\Register;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Register\RegisterUserDto;
use App\State\Register\RegisterUserProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: RegisterUserDto::class,
            read: false,
            validate: true,
            processor: RegisterUserProcessor::class,
        )
    ],
    extraProperties: ['tag' => 'Auth'],
)]
final class RegisterUserResource
{

}
