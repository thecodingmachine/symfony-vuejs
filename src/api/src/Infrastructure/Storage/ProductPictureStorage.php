<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Store\ProductPictureStore;
use App\Domain\Throwable\Invalid\InvalidProductPicture;

final class ProductPictureStorage extends PublicStorage implements ProductPictureStore
{
    protected function getDirectoryName(): string
    {
        return 'product_picture';
    }

    /**
     * @param ProductPicture[] $pictures
     *
     * @return string[]
     *
     * @throws InvalidProductPicture
     */
    public function writeAll(array $pictures): array
    {
        foreach ($pictures as $picture) {
            $violations = $this->validator->validate($picture);
            InvalidProductPicture::throwException($violations);
        }

        return parent::putAll($pictures);
    }
}
