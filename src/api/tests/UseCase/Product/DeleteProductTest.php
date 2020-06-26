<?php

declare(strict_types=1);

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Storable\ProductPicture;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProduct;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
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
    'sends a task for deleting the pictures',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $deleteProduct = self::$container->get(DeleteProduct::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        $productDao    = self::$container->get(ProductDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($deleteProduct instanceof DeleteProduct);
        assert($transport instanceof InMemoryTransport);
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
        assertCount(1, $transport->getSent());

        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteProductPicturesTask);

        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class);
