<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUp;

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class SignUp
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @throws WrongRole
     *
     * @Mutation
     * @Assertion(for="firstName", constraint={@Assert\NotBlank(message="not_blank"), @Assert\Length(max=255, maxMessage="max_length_255")})
     * @Assertion(for="lastName", constraint={@Assert\NotBlank(message="not_blank"), @Assert\Length(max=255, maxMessage="max_length_255")})
     * @Assertion(for="email", constraint={@Assert\NotBlank(message="not_blank"), @Assert\Length(max=255, maxMessage="max_length_255"), @Assert\Email(message="invalid_email")})
     */
    public function signUp(
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ): bool {
        if ($role === Role::ADMINISTRATOR()) {
            throw new WrongRole();
        }

        // As there is no security on this endpoint,
        // we make sure that no one is able to check
        // if an e-mail exists according to response time.
        $task = new SignUpTask(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
        $this->messageBus->dispatch($task);

        return true;
    }
}
