<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteProduct
{
    private ProductDao $productDao;
    private MessageBusInterface $messageBus;

    public function __construct(
        ProductDao $productDao,
        MessageBusInterface $messageBus
    ) {
        $this->productDao = $productDao;
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     */
    public function deleteProduct(Product $product): bool
    {
        $pictures = $product->getPictures();
        $this->productDao->delete($product, true);

        if (empty($pictures)) {
            return true;
        }

        // As the deletion of all the pictures might
        // take some time, we do it asynchronously.
        $task = new DeleteProductsPicturesTask($pictures);
        $this->messageBus->dispatch($task);

        return true;
    }
}
