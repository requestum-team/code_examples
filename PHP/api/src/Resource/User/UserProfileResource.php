<?php declare(strict_types=1);

namespace App\Resource\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Dto\User\UpdateUserProfileDto;
use App\Entity\User;
use App\State\User\GetCurrentUserProvider;
use App\State\User\UpdateUserProfileProcessor;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/me',
            normalizationContext: ['groups' => ['user:read']],
            output: User::class,
            provider: GetCurrentUserProvider::class,
        ),
        new Patch(
            uriTemplate: '/users/me',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            normalizationContext: ['groups' => ['user:read']],
            input: UpdateUserProfileDto::class,
            output: User::class,
            processor: UpdateUserProfileProcessor::class,
        ),
    ],
    extraProperties: ['tag' => 'User']
)]
final class UserProfileResource
{

}
