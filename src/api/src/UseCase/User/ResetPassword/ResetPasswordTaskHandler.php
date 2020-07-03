<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Throwable\BusinessRule;
use App\Domain\Throwable\NotFound;
use DateInterval;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResetPasswordTaskHandler implements MessageHandlerInterface
{
    private UserDao $userDao;
    private ResetPasswordTokenDao $resetPasswordTokenDao;
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function __construct(
        UserDao $userDao,
        ResetPasswordTokenDao $resetPasswordTokenDao,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->userDao               = $userDao;
        $this->resetPasswordTokenDao = $resetPasswordTokenDao;
        $this->messageBus            = $messageBus;
        $this->logger                = $logger;
    }

    public function __invoke(ResetPasswordTask $task): void
    {
        try {
            $this->resetPassword($task->getEmail());
        } catch (BusinessRule $e) {
            // We do not want to throw a domain exception
            // as this task would be retried otherwise.
            // Indeed, a domain exception occurs when a business rule
            // is not fulfilled. If it happens, it will happened every time
            // we retry this task.
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @throws NotFound
     */
    private function resetPassword(string $email): void
    {
        $user = $this->userDao->mustFindOneByEmail($email);

        $plainToken = Uuid::uuid4()->toString();
        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->add(new DateInterval('P1D')); // Add one day to current date time.

        // Check if there is already a token for this user.
        // If so, we delete it.
        $resetPasswordToken = $user->getResetPasswordToken();
        if ($resetPasswordToken !== null) {
            $this->resetPasswordTokenDao->delete($resetPasswordToken);
        }

        $resetPasswordToken = new ResetPasswordToken($user, $plainToken, $validUntil);
        $this->resetPasswordTokenDao->save($resetPasswordToken);

        $notification = new ResetPasswordNotification($user, $resetPasswordToken, $plainToken);
        $this->messageBus->dispatch($notification);
    }
}
