<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUpClient;

use App\Domain\Throwable\BusinessRule;
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

    public function __invoke(SignUpClientTask $task): void
    {
        try {
            $this->signUpClient->signUp(
                $task->getFirstName(),
                $task->getLastName(),
                $task->getEmail(),
                $task->getLocale()
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
