<?php declare(strict_types=1);

namespace App\Resource\User;

use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\User\SetAvatarDto;
use App\State\User\SetAvatarProcessor;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/users/me/avatar',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: SetAvatarDto::class,
            processor: SetAvatarProcessor::class,
            extraProperties: ['swagger_context' => ['tags' => ['User']]],
        )
    ],
    extraProperties: ['tag' => 'User'],
)]
final class SetAvatarResource
{

}
