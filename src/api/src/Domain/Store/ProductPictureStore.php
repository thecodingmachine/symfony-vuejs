<?php

declare(strict_types=1);

namespace App\Domain\Store;

use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Throwable\Invalid\InvalidProductPicture;

interface ProductPictureStore
{
    /**
     * @param ProductPicture[] $pictures
     *
     * @return string[]
     *
     * @throws InvalidProductPicture
     */
    public function writeAll(array $pictures): array;

    /**
     * @param string[] $fileNames
     */
    public function deleteAll(array $fileNames): void;

    public function delete(string $file): void;

    public function fileExists(string $fileName): bool;
}
