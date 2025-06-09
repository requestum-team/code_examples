<?php declare(strict_types=1);

namespace App\Resource\Image;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Image;
use App\State\Image\UserImagesProvider;

#[ApiResource(
    shortName: 'MyImages',
    operations: [
        new GetCollection(
            uriTemplate: '/users/me/images',
            paginationEnabled: false,
            output: Image::class,
            name: 'my_images',
            provider: UserImagesProvider::class,
        )
    ],
    normalizationContext: ['groups' => ['image:read']],
    extraProperties: ['tag' => 'User'],
)]
final class UserImageCollectionResource
{

}
