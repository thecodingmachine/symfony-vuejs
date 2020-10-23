<?php

declare(strict_types=1);

namespace App\Domain\Storage;

final class ProductPictureStorage extends PublicStorage
{
    protected function getDirectoryName(): string
    {
        return $this->parameters->get('app.public_storage_product_picture');
    }
}
