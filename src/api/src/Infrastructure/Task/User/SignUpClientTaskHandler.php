<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\User;

use App\Application\User\SignUpClient;
use App\Domain\Throwable\BaseThrowable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SignUpClientTaskHandler implements MessageHandlerInterface
{
    private SignUpClient $signUpClient;
    private LoggerInterface $logger;

    public function __construct(
        SignUpClient $signUpClient,
        LoggerInterface $logger
    ) {
        $this->signUpClient = $signUpClient;
        $this->logger       = $logger;
    }

    public function __invoke(SignUpClientTask $task) : void
    {
        try {
            $this->signUpClient->signUp(
                $task->getFirstName(),
                $task->getLastName(),
                $task->getEmail()
            );
        } catch (BaseThrowable $e) {
            // We do not want to throw a domain exception
            // as this task will be retried otherwise.
            // Indeed, a domain exception occurs when a business rule
            // is not fulfilled. If it happens, it will happened every time
            // we retry this task.
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
