<?php

declare(strict_types=1);

namespace App\Application\User\ResetPassword;

use App\Domain\Model\ResetPasswordToken;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use DateInterval;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResetPassword
{
    private UserRepository $userRepository;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private MessageBusInterface $messageBus;

    public function __construct(
        UserRepository $userRepository,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        MessageBusInterface $messageBus
    ) {
        $this->userRepository               = $userRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->messageBus                   = $messageBus;
    }

    /**
     * @throws UserNotFoundByEmail
     */
    public function reset(string $email) : void
    {
        $user = $this->userRepository->mustFindOneByEmail($email);

        $plainToken = Uuid::uuid4()->toString();
        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->add(new DateInterval('P1D')); // Add one day to current date time.

        // Check if there is already a token for this user.
        // If so, we delete it.
        $resetPasswordToken = $user->getResetPasswordToken();
        if ($resetPasswordToken !== null) {
            $this->resetPasswordTokenRepository->delete($resetPasswordToken);
        }

        $resetPasswordToken = new ResetPasswordToken($user, $plainToken, $validUntil);
        $this->resetPasswordTokenRepository->save($resetPasswordToken);

        $notification = new ResetPasswordNotification($user, $resetPasswordToken, $plainToken);
        $this->messageBus->dispatch($notification);
    }
}
