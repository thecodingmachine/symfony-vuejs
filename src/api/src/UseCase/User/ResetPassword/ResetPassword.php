<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Model\ResetPasswordToken;
use DateInterval;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assertion;

final class ResetPassword
{
    private UserDao $userDao;
    private ResetPasswordTokenDao $resetPasswordTokenDao;
    private CreateResetPasswordEmail $createResetPasswordEmail;
    private MailerInterface $mailer;

    public function __construct(
        UserDao $userDao,
        ResetPasswordTokenDao $resetPasswordTokenDao,
        CreateResetPasswordEmail $createResetPasswordEmail,
        MailerInterface $mailer
    ) {
        $this->userDao                  = $userDao;
        $this->resetPasswordTokenDao    = $resetPasswordTokenDao;
        $this->createResetPasswordEmail = $createResetPasswordEmail;
        $this->mailer                   = $mailer;
    }

    /**
     * @Mutation
     * @Assertion(for="email", constraint={@Assert\NotBlank(message="not_blank"), @Assert\Length(max=255, maxMessage="max_length_255"), @Assert\Email(message="invalid_email")})
     */
    public function resetPassword(string $email): bool
    {
        $user = $this->userDao->findOneByEmail($email);
        if ($user === null) {
            return true;
        }

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

        $email = $this->createResetPasswordEmail->createEmail($user, $resetPasswordToken, $plainToken);
        $this->mailer->send($email);

        return true;
    }
}
