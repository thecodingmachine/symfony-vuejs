<?php

declare(strict_types=1);

namespace App\UseCase\Product\DeleteProductPictures;

use App\Domain\Storage\ProductPictureStorage;

final class DeleteProductPictures
{
    private ProductPictureStorage $productPicturesStorage;

    public function __construct(ProductPictureStorage $productPicturesStorage)
    {
        $this->productPicturesStorage = $productPicturesStorage;
    }

    /**
     * @param string[] $pictures
     */
    public function deleteProductPictures(array $pictures): void
    {
        $this->productPicturesStorage->deleteAll($pictures);
    }
}
