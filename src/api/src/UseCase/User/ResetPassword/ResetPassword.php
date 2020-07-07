<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class ResetPassword
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     * @Assertion(for="email", constraint={@Assert\NotBlank(message="not_blank"), @Assert\Length(max=255, maxMessage="max_length_255"), @Assert\Email(message="invalid_email")})
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
