<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Infrastructure\Task\User\ResetPasswordTask;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class ResetPasswordController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     * @Assertion(for="email", constraint={@Assert\NotBlank, @Assert\Length(max = 255), @Assert\Email})
     */
    public function resetPassword(string $email): bool
    {
        // As there is no security on this endpoint,
        // we make sure that no one is able to check
        // if an e-mail exists according to response time.
        $task = new ResetPasswordTask($email);
        $this->messageBus->dispatch($task);

        return true;
    }
}
