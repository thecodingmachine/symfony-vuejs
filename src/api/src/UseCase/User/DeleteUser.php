<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Model\User;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteUser
{
    private UserDao $userDao;
    private MessageBusInterface $messageBus;

    public function __construct(
        UserDao $userDao,
        MessageBusInterface $messageBus
    ) {
        $this->userDao    = $userDao;
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     */
    public function deleteUser(User $user): bool
    {
        // If the user is a merchant, we have to
        // delete all files related to its companies'
        // products.
        $pictures = $user->getProductsPictures();
        $this->userDao->delete($user, true);

        if (! empty($productsPictures)) {
            $task = new DeleteProductsPicturesTask($productsPictures);
            $this->messageBus->dispatch($task);
        }

        return true;
    }
}
