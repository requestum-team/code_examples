<?php declare(strict_types=1);

namespace App\Resource\TwoFactor;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\TwoFactor\Verify2FADto;
use App\State\TwoFactor\TwoFactorVerifyProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/2fa/verify',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            denormalizationContext: ['groups' => ['write']],
            input: Verify2FADto::class,
            read: false,
            deserialize: true,
            validate: true,
            processor: TwoFactorVerifyProcessor::class,
        )
    ],
    extraProperties: ['tag' => '2FA'],
)]
final class VerifyTwoFactorResource
{

}
