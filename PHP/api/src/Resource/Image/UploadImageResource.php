<?php declare(strict_types=1);

namespace App\Resource\Image;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Image\UploadImageDto;
use App\Entity\Image;
use App\State\Image\UploadImageProcessor;

#[ApiResource(
    shortName: 'ImageUpload',
    operations: [
        new Post(
            uriTemplate: '/images',
            inputFormats: ['multipart' => ['multipart/form-data']],
            normalizationContext: ['groups' => ['image:read']],
            input: UploadImageDto::class,
            output: Image::class,
            deserialize: false,
            processor: UploadImageProcessor::class,
        )
    ],
    extraProperties: ['tag' => 'User'],
)]
final class UploadImageResource
{

}
