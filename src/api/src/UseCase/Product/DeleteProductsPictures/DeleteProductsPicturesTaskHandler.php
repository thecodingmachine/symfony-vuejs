<?php

declare(strict_types=1);

namespace App\UseCase\Product\DeleteProductsPictures;

use App\Domain\Storage\ProductPictureStorage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeleteProductsPicturesTaskHandler implements MessageHandlerInterface
{
    private ProductPictureStorage $productPicturesStorage;

    public function __construct(ProductPictureStorage $productPicturesStorage)
    {
        $this->productPicturesStorage = $productPicturesStorage;
    }

    public function __invoke(DeleteProductsPicturesTask $task): void
    {
        $this->productPicturesStorage->deleteAll(
            $task->getPictures()
        );
    }
}
