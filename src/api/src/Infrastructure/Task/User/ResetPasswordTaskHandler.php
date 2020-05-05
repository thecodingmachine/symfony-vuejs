<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\User;

use App\Application\User\ResetPassword\ResetPassword;
use App\Domain\Throwable\BaseThrowable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordTaskHandler implements MessageHandlerInterface
{
    private ResetPassword $resetPassword;
    private LoggerInterface $logger;

    public function __construct(
        ResetPassword $resetPassword,
        LoggerInterface $logger
    ) {
        $this->resetPassword = $resetPassword;
        $this->logger        = $logger;
    }

    public function __invoke(ResetPasswordTask $task) : void
    {
        try {
            $this->resetPassword->reset($task->getEmail());
        } catch (BaseThrowable $e) {
            // We do not want to throw a domain exception
            // as this task would be retried otherwise.
            // Indeed, a domain exception occurs when a business rule
            // is not fulfilled. If it happens, it will happened every time
            // we retry this task.
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
