<?php declare(strict_types=1);

namespace App\Resource\TwoFactor;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\TwoFactor\TwoFactorEnableProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/2fa/enable',
            inputFormats: [
                'json' => ['application/json'],
                'jsonld' => ['application/ld+json'],
            ],
            input: false,
            output: false,
            read: false,
            processor: TwoFactorEnableProcessor::class,
        )
    ],
    extraProperties: ['tag' => '2FA'],
)]
final class EnableTwoFactorResource
{

}
