<?php

declare(strict_types=1);

namespace App\Application\Product;

use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProductRepository;
use App\Domain\Store\ProductPictureStore;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use Throwable;

final class CreateProduct
{
    private ProductRepository $productRepository;
    private CompanyRepository $companyRepository;
    private ProductPictureStore $productPictureStore;

    public function __construct(
        ProductRepository $productRepository,
        CompanyRepository $companyRepository,
        ProductPictureStore $productPictureStore
    ) {
        $this->productRepository   = $productRepository;
        $this->companyRepository   = $companyRepository;
        $this->productPictureStore = $productPictureStore;
    }

    /**
     * @param ProductPicture[]|null $pictures
     *
     * @throws ProductWithNameExists
     * @throws CompanyNotFoundById
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     */
    public function create(
        string $name,
        float $price,
        string $companyId,
        ?array $pictures = null
    ): Product {
        $this->productRepository->mustNotFindOneByName($name);
        $company = $this->companyRepository->mustFindOneById($companyId);

        $fileNames = $pictures !== null ?
            $this->productPictureStore->writeAll($pictures) :
            null;

        $product = new Product(
            $company,
            $name,
            $price
        );
        $product->setPictures($fileNames);

        try {
            $this->productRepository->save($product);
        } catch (InvalidProduct $e) {
            // pepakriz/phpstan-exception-rules limitation: "Catch statement does not know about runtime subtypes".
            // See https://github.com/pepakriz/phpstan-exception-rules#catch-statement-does-not-know-about-runtime-subtypes.
            $this->beforeThrowDeletePicturesIfExist($fileNames);

            throw $e;
        } catch (Throwable $e) {
            // If any exception occurs, delete
            // the pictures from the store.
            $this->beforeThrowDeletePicturesIfExist($fileNames);

            throw $e;
        }

        return $product;
    }

    /**
     * @param string[]|null $fileNames
     */
    private function beforeThrowDeletePicturesIfExist(?array $fileNames): void
    {
        if ($fileNames === null) {
            return;
        }

        $this->productPictureStore->deleteAll($fileNames);
    }
}
