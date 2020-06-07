<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Infrastructure\Task\User\SignUpClientTask;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class SignUpClientController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
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
}
