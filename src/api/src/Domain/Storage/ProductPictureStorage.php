<?php

declare(strict_types=1);

namespace App\Domain\Storage;

final class ProductPictureStorage extends PublicStorage
{
    protected function getDirectoryName(): string
    {
        return 'product_picture';
    }
}
