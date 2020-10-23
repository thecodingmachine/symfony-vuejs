<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Company;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\Domain\Throwable\InvalidModel;
use App\Domain\Throwable\InvalidStorable;
use Psr\Http\Message\UploadedFileInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class CreateProduct
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
     * @param UploadedFileInterface[]|null $pictures
     *
     * @throws InvalidModel
     * @throws InvalidStorable
     *
     * @Mutation
     * @Logged
     * @Security("is_granted('CREATE_PRODUCT', company)")
     */
    public function createProduct(
        string $name,
        float $price,
        Company $company,
        ?array $pictures = null
    ): Product {
        $storables = null;
        if ($pictures !== null) {
            $storables = ProductPicture::createAllFromUploadedFiles(
                $pictures
            );
        }

        return $this->create(
            $name,
            $price,
            $company,
            $storables
        );
    }

    /**
     * @param ProductPicture[]|null $pictures
     *
     * @throws InvalidModel
     * @throws InvalidStorable
     */
    public function create(
        string $name,
        float $price,
        Company $company,
        ?array $pictures = null
    ): Product {
        $product = new Product(
            $company,
            $name,
            $price
        );

        // Validate the product before uploading
        // its pictures.
        $this->productDao->validate($product);

        // Upload the pictures (if any).
        // Note: the validation of those pictures
        // is done directly in the writeAll function.
        if (! empty($pictures)) {
            $filenames = $this->productPictureStorage->writeAll($pictures);
            $product->setPictures($filenames);
        }

        $this->productDao->save($product);

        return $product;
    }
}
