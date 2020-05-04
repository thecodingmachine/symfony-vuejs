<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Infrastructure\Task\User\SignUpClientTask;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class SignUpClientController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     */
    public function signUpClient(
        string $firstName,
        string $lastName,
        string $email
    ) : bool {
        // As there is no security on this endpoint,
        // we make sure that no one is able to check
        // if an e-mail exists according to response time.
        $task = new SignUpClientTask(
            $firstName,
            $lastName,
            $email
        );
        $this->messageBus->dispatch($task);

        return true;
    }
}
