<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\Domain\Storage\ProductPictureStorage;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class DeleteProduct
{
    private ProductDao $productDao;
    private ProductPictureStorage $productPictureStorage;

    public function __construct(
        ProductDao $productDao,
        ProductPictureStorage $productPictureStorage
    ) {
        $this->productDao            = $productDao;
        $this->productPictureStorage = $productPictureStorage;
    }

    /**
     * @Mutation
     * @Security("is_granted('CAN_DELETE', product)")
     */
    public function deleteProduct(Product $product): bool
    {
        $pictures = $product->getPictures();

        $this->productDao->delete($product);
        if ($pictures === null) {
            return true;
        }

        $this->productPictureStorage->deleteAll($pictures);

        return true;
    }
}
