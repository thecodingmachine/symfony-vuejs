<?php

declare(strict_types=1);

namespace App\UseCase\Product\DeleteProductPictures;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeleteProductPicturesTaskHandler implements MessageHandlerInterface
{
    private DeleteProductPictures $deleteProductPictures;

    public function __construct(DeleteProductPictures $deleteProductPictures)
    {
        $this->deleteProductPictures = $deleteProductPictures;
    }

    public function __invoke(DeleteProductPicturesTask $task): void
    {
        $this->deleteProductPictures->deleteProductPictures(
            $task->getPictures()
        );
    }
}
