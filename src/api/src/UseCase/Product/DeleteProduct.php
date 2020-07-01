<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

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
     * @Security("is_granted('NA', product)")
     */
    public function deleteProduct(Product $product): bool
    {
        $pictures = $product->getPictures();
        $this->productDao->delete($product, true);

        if (empty($pictures)) {
            return true;
        }

        $task = new DeleteProductPicturesTask($pictures);
        $this->messageBus->dispatch($task);

        return true;
    }
}
