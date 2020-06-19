<?php

declare(strict_types=1);

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProduct;
use TheCodingMachine\TDBM\TDBMException;

it(
    'deletes the product',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $deleteProduct = self::$container->get(DeleteProduct::class);
        $productDao    = self::$container->get(ProductDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($deleteProduct instanceof DeleteProduct);
        assert($productDao instanceof ProductDao);

        $company = $createCompany->create('foo');
        $product = $createProduct->create(
            'foo',
            1,
            $company,
            null
        );

        $deleteProduct->deleteProduct($product);
        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class);

it(
    'deletes the pictures',
    function (): void {
        $createCompany         = self::$container->get(CreateCompany::class);
        $createProduct         = self::$container->get(CreateProduct::class);
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        $deleteProduct         = self::$container->get(DeleteProduct::class);
        $productDao            = self::$container->get(ProductDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStorage instanceof  ProductPictureStorage);
        assert($deleteProduct instanceof DeleteProduct);
        assert($productDao instanceof ProductDao);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ];

        $storables = ProductPicture::createAllFromPaths(
            $pictures
        );

        $company = $createCompany->create('foo');
        $product = $createProduct->create(
            'foo',
            1,
            $company,
            $storables
        );

        $deleteProduct->deleteProduct($product);
        foreach ($storables as $storable) {
            assertFalse($productPictureStorage->fileExists($storable->getFilename()));
        }

        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class);
