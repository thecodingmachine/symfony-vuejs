<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUp;

use App\Domain\Throwable\BusinessRule;
use App\UseCase\User\CreateUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SignUpTaskHandler implements MessageHandlerInterface
{
    private CreateUser $createUser;
    private LoggerInterface $logger;

    public function __construct(
        CreateUser $createUser,
        LoggerInterface $logger
    ) {
        $this->createUser = $createUser;
        $this->logger     = $logger;
    }

    public function __invoke(SignUpTask $task): void
    {
        try {
            $this->createUser->createUser(
                $task->getFirstName(),
                $task->getLastName(),
                $task->getEmail(),
                $task->getLocale(),
                $task->getRole()
            );
        } catch (BusinessRule $e) {
            // We do not want to throw a domain exception
            // as this task would be retried otherwise.
            // Indeed, a domain exception occurs when a business rule
            // is not fulfilled. If it happens, it will happened every time
            // we retry this task.
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
