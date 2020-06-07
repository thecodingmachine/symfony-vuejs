<?php

declare(strict_types=1);

use App\Application\Company\CreateCompany;
use App\Application\Product\CreateProduct;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Store\ProductPictureStore;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use App\Infrastructure\Factory\StorableFactory;
use App\Tests\Application\DummyValues;

it(
    'creates a product',
    function (
        string $name,
        float $price
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);

        $company = $createCompany->create('foo');
        $product = $createProduct->create(
            $name,
            $price,
            $company->getId()
        );

        assertEquals($name, $product->getName());
        assertEquals($price, $product->getPrice());
        assertEquals($company->getId(), $product->getCompany()->getId());
        assertNull($product->getPictures());
    }
)
    ->with([
        ['foo', 1],
        ['foo', 1.0],
    ]);

it(
    'stores the pictures',
    function (): void {
        $createCompany       = self::$container->get(CreateCompany::class);
        $createProduct       = self::$container->get(CreateProduct::class);
        $productPictureStore = self::$container->get(ProductPictureStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStore instanceof  ProductPictureStore);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ];
        /** @var ProductPicture[] $storables */
        $storables = StorableFactory::createAllFromPaths(
            $pictures,
            ProductPicture::class
        );

        $company = $createCompany->create('foo');
        $product = $createProduct->create(
            'foo',
            1,
            $company->getId(),
            $storables
        );

        assertNotNull($product->getPictures());
        foreach ($product->getPictures() as $picture) {
            assertTrue($productPictureStore->fileExists($picture));
        }
    }
);

it(
    'throws an exception if name is already associated to a product',
    function (
        string $name,
        float $price
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);

        $company = $createCompany->create('foo');

        $createProduct->create(
            $name,
            $price,
            $company->getId()
        );

        $createProduct->create(
            $name,
            $price,
            $company->getId()
        );
    }
)
    ->with([
        ['foo', 1],
    ])
    ->throws(ProductWithNameExists::class);

it(
    'throws an exception if company does not exist',
    function (): void {
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);

        $createProduct->create(
            'foo',
            1,
            'foo'
        );
    }
)
    ->throws(CompanyNotFoundById::class);

it(
    'throws an exception if invalid product picture',
    function (): void {
        $createCompany       = self::$container->get(CreateCompany::class);
        $createProduct       = self::$container->get(CreateProduct::class);
        $productPictureStore = self::$container->get(ProductPictureStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStore instanceof  ProductPictureStore);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.txt',
        ];
        /** @var ProductPicture[] $storables */
        $storables = StorableFactory::createAllFromPaths(
            $pictures,
            ProductPicture::class
        );

        $company = $createCompany->create('foo');
        $createProduct->create(
            'foo',
            1,
            $company->getId(),
            $storables
        );

        foreach ($storables as $storable) {
            assertFalse($productPictureStore->fileExists(
                $storable->getGeneratedFileName()
            ));
        }
    }
)
    ->throws(InvalidProductPicture::class);

it(
    'throws an exception if invalid product data',
    function (
        string $name,
        float $price
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);

        $company = $createCompany->create('foo');
        $createProduct->create(
            $name,
            $price,
            $company->getId()
        );
    }
)
    ->with([
        // Blank name.
        [DummyValues::BLANK, 1],
        // Name > 255.
        [DummyValues::CHAR256, 1],
        // Negative price.
        ['foo', -1],
        // No price.
        ['foo', 0],
    ])
    ->throws(InvalidProduct::class);

it(
    'deletes the pictures if exception',
    function (): void {
        $createCompany       = self::$container->get(CreateCompany::class);
        $createProduct       = self::$container->get(CreateProduct::class);
        $productPictureStore = self::$container->get(ProductPictureStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStore instanceof  ProductPictureStore);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ];
        /** @var ProductPicture[] $storables */
        $storables = StorableFactory::createAllFromPaths(
            $pictures,
            ProductPicture::class
        );

        $company = $createCompany->create('foo');
        $createProduct->create(
            DummyValues::BLANK,
            1,
            $company->getId(),
            $storables
        );

        foreach ($storables as $storable) {
            assertFalse($productPictureStore->fileExists(
                $storable->getGeneratedFileName()
            ));
        }
    }
)
    ->throws(InvalidProduct::class);
