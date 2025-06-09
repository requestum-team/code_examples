<?php declare(strict_types=1);

namespace App\Dto\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


class UploadImageDto
{
    #[Assert\NotNull]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Please upload a valid image file'
    )]
    public ?UploadedFile $file = null;
}
