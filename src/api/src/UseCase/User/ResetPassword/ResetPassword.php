<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use DateInterval;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class ResetPassword
{
    private UserDao $userDao;
    private ResetPasswordTokenDao $resetPasswordTokenDao;
    private MessageBusInterface $messageBus;

    public function __construct(
        UserDao $userDao,
        ResetPasswordTokenDao $resetPasswordTokenDao,
        MessageBusInterface $messageBus
    ) {
        $this->userDao               = $userDao;
        $this->resetPasswordTokenDao = $resetPasswordTokenDao;
        $this->messageBus            = $messageBus;
    }

    /**
     * @Mutation
     * @Assertion(for="email", constraint={@Assert\NotBlank, @Assert\Length(max = 255), @Assert\Email})
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

    /**
     * @throws UserNotFoundByEmail
     */
    public function reset(string $email): ResetPasswordNotification
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

        return $notification;
    }
}
