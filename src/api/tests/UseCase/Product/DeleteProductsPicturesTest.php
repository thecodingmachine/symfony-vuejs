<?php

declare(strict_types=1);

use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTaskHandler;

use function PHPUnit\Framework\assertFalse;

it(
    'deletes pictures',
    function (): void {
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        assert($productPictureStorage instanceof ProductPictureStorage);
        $deleteProductsPicturesTaskHandler = self::$container->get(DeleteProductsPicturesTaskHandler::class);
        assert($deleteProductsPicturesTaskHandler instanceof DeleteProductsPicturesTaskHandler);

        $storables = ProductPicture::createAllFromPaths([
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ]);
        $filenames = $productPictureStorage->writeAll($storables);

        $task = new App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask($filenames);
        $deleteProductsPicturesTaskHandler($task);

        foreach ($filenames as $filename) {
            assertFalse($productPictureStorage->fileExists($filename));
        }
    }
)
    ->group('product');
