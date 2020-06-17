<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUpClient;

use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Throwable\Exists\UserWithEmailExists;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\UseCase\User\CreateUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class SignUpClient
{
    private CreateUser $createUser;
    private MessageBusInterface $messageBus;

    public function __construct(CreateUser $createUser, MessageBusInterface $messageBus)
    {
        $this->createUser = $createUser;
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     * @Assertion(for="firstName", constraint={@Assert\NotBlank, @Assert\Length(max = 255)})
     * @Assertion(for="lastName", constraint={@Assert\NotBlank, @Assert\Length(max = 255)})
     * @Assertion(for="email", constraint={@Assert\NotBlank, @Assert\Length(max = 255), @Assert\Email})
     * @Assertion(for="locale", constraint={@Assert\Choice(callback={"App\Domain\Enum\LocaleEnum", "values"})})
     */
    public function signUpClient(
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ): bool {
        // As there is no security on this endpoint,
        // we make sure that no one is able to check
        // if an e-mail exists according to response time.
        $task = new SignUpClientTask(
            $firstName,
            $lastName,
            $email,
            $locale
        );
        $this->messageBus->dispatch($task);

        return true;
    }

    /**
     * @throws UserWithEmailExists
     * @throws InvalidUser
     * @throws UserNotFoundByEmail
     */
    public function signUp(
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ): User {
        return $this
            ->createUser
            ->createUser(
                $firstName,
                $lastName,
                $email,
                $locale,
                RoleEnum::CLIENT
            );
    }
}
