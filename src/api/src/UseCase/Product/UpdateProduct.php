<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Storage\ProductPictureStorage;
use App\Domain\Throwable\InvalidModel;
use App\Domain\Throwable\InvalidStorable;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

use function array_diff;
use function array_merge;

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
     * @throws InvalidModel
     * @throws InvalidStorable
     *
     * @Mutation
     * @Logged
     * @Security("is_granted('UPDATE_PRODUCT', product)")
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
     * @throws InvalidModel
     * @throws InvalidStorable
     */
    public function update(
        Product $product,
        string $name,
        float $price,
        ?array $newPictures = null,
        ?array $picturesToDelete = null
    ): Product {
        $product->setName($name);
        $product->setPrice($price);

        // Validate the product before uploading
        // its new pictures.
        $this->productDao->validate($product);

        $pictures = $product->getPictures() ?: []; // If null pictures, create an empty array.

        // Upload the new pictures (if any).
        // Note: the validation of those pictures
        // is done directly in the writeAll function.
        if (! empty($newPictures)) {
            $newFilenames = $this->productPictureStorage->writeAll($newPictures);
            $pictures     = array_merge($pictures, $newFilenames);
        }

        // Delete some pictures (if any).
        if (! empty($picturesToDelete)) {
            // As the deletion of all the pictures might
            // take some time, we do it asynchronously.
            $task = new DeleteProductsPicturesTask($picturesToDelete);
            $this->messageBus->dispatch($task);

            // Remove from the current list of pictures
            // the pictures we are going to delete.
            $pictures = array_diff($pictures, $picturesToDelete);
        }

        $pictures = empty($pictures) ? null : $pictures; // If empty pictures, set it as null.
        $product->setPictures($pictures);

        $this->productDao->save($product);

        return $product;
    }
}
