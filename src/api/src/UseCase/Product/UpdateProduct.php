<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;
use Throwable;

final class UpdateProduct
{
    private ProductDao $productDao;
    private ProductPictureStorage $productPictureStorage;
    private MessageBusInterface $messageBus;

    public function __construct(
        ProductDao $productDao,
        ProductPictureStorage $productPictureStorage,
        MessageBusInterface $messageBus
    ) {
        $this->productDao            = $productDao;
        $this->productPictureStorage = $productPictureStorage;
        $this->messageBus            = $messageBus;
    }

    /**
     * @param UploadedFileInterface[]|null $newPictures
     * @param string[]|null                $picturesToDelete
     *
     * @throws ProductWithNameExists
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     *
     * @Mutation
     * @Security("is_granted('NA', product)")
     */
    public function updateProduct(
        Product $product,
        string $name,
        float $price,
        ?array $newPictures = null,
        ?array $picturesToDelete = null
    ): Product {
        $storables = null;
        if ($newPictures !== null) {
            $storables = ProductPicture::createAllFromUploadedFiles(
                $newPictures
            );
        }

        return $this->update(
            $product,
            $name,
            $price,
            $storables,
            $picturesToDelete
        );
    }

    /**
     * @param ProductPicture[]|null $newPictures
     * @param string[]|null         $picturesToDelete
     *
     * @throws ProductWithNameExists
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     */
    public function update(
        Product $product,
        string $name,
        float $price,
        ?array $newPictures = null,
        ?array $picturesToDelete = null
    ): Product {
        $this->productDao->mustNotFindOneByName($name, $product->getId());

        $newFilenames = null;
        if (! empty($newPictures)) {
            $newFilenames = $this->productPictureStorage->writeAll($newPictures);
        }

        $product->setName($name);
        $product->setPrice($price);
        $product->setPictures($newFilenames);

        try {
            $this->productDao->save($product);
        } catch (InvalidProduct $e) {
            // pepakriz/phpstan-exception-rules limitation: "Catch statement does not know about runtime subtypes".
            // See https://github.com/pepakriz/phpstan-exception-rules#catch-statement-does-not-know-about-runtime-subtypes.
            $this->beforeThrowDeleteNewPicturesIfExist($newFilenames);

            throw $e;
        } catch (Throwable $e) {
            // If any exception occurs, delete
            // the new pictures from the store.
            $this->beforeThrowDeleteNewPicturesIfExist($newFilenames);

            throw $e;
        }

        if (! empty($picturesToDelete)) {
            $task = new DeleteProductPicturesTask($picturesToDelete);
            $this->messageBus->dispatch($task);
        }

        return $product;
    }

    /**
     * @param string[]|null $newFilenames
     */
    private function beforeThrowDeleteNewPicturesIfExist(?array $newFilenames): void
    {
        if ($newFilenames === null) {
            return;
        }

        $task = new DeleteProductPicturesTask($newFilenames);
        $this->messageBus->dispatch($task);
    }
}
