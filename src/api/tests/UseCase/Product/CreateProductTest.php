<?php

declare(strict_types=1);

use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\Tests\UseCase\AsyncTransport;
use App\Tests\UseCase\DummyValues;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

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
            $company
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
        $createCompany         = self::$container->get(CreateCompany::class);
        $createProduct         = self::$container->get(CreateProduct::class);
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStorage instanceof  ProductPictureStorage);

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

        assertNotNull($product->getPictures());
        foreach ($product->getPictures() as $picture) {
            assertTrue($productPictureStorage->fileExists($picture));
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
            $company
        );

        $createProduct->create(
            $name,
            $price,
            $company
        );
    }
)
    ->with([
        ['foo', 1],
    ])
    ->throws(ProductWithNameExists::class);

it(
    'throws an exception if invalid product picture',
    function (): void {
        $createCompany         = self::$container->get(CreateCompany::class);
        $createProduct         = self::$container->get(CreateProduct::class);
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($productPictureStorage instanceof  ProductPictureStorage);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.txt',
        ];

        $storables = ProductPicture::createAllFromPaths(
            $pictures
        );

        $company = $createCompany->create('foo');
        $createProduct->create(
            'foo',
            1,
            $company,
            $storables
        );

        foreach ($storables as $storable) {
            assertFalse($productPictureStorage->fileExists(
                $storable->getFilename()
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
            $company
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
    'sends a task for deleting the pictures if exception',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($transport instanceof InMemoryTransport);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ];

        $storables = ProductPicture::createAllFromPaths(
            $pictures
        );

        $company = $createCompany->create('foo');
        $createProduct->create(
            DummyValues::BLANK,
            1,
            $company,
            $storables
        );

        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteProductPicturesTask);
    }
)
    ->throws(InvalidProduct::class);
