<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Store\ProductPictureStore;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use Throwable;

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

        $storedPictures = [];
        foreach ($pictures as $picture) {
            try {
                parent::put(
                    $picture->getGeneratedFileName(),
                    $picture->getResource()
                );

                $storedPictures[] = $picture->getGeneratedFileName();
            } catch (Throwable $e) {
                // If any exception occurs, delete
                // already stored pictures.
                $this->deleteAll($storedPictures);

                throw $e;
            }
        }

        return $storedPictures;
    }

    /**
     * @param string[] $fileNames
     */
    public function deleteAll(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            parent::delete($fileName);
        }
    }
}
