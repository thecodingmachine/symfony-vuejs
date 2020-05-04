<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Infrastructure\Task\User\ResetPasswordTask;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class ResetPasswordController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     */
    public function resetPassword(string $email) : bool
    {
        // As there is no security on this endpoint,
        // we make sure that no one is able to check
        // if an e-mail exists according to response time.
        $task = new ResetPasswordTask($email);
        $this->messageBus->dispatch($task);

        return true;
    }
}
