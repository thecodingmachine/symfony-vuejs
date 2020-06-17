<?php

declare(strict_types=1);

namespace App\UseCase\User\UpdatePassword;

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Model\Proxy\PasswordProxy;
use App\Domain\Model\User;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use Safe\DateTimeImmutable;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

use function password_verify;

final class UpdatePassword
{
    private ResetPasswordTokenDao $resetPasswordTokenDao;
    private UserDao $userDao;

    public function __construct(
        ResetPasswordTokenDao $resetPasswordTokenDao,
        UserDao $userDao
    ) {
        $this->resetPasswordTokenDao = $resetPasswordTokenDao;
        $this->userDao               = $userDao;
    }

    /**
     * @throws ResetPasswordTokenNotFoundById
     * @throws WrongResetPasswordToken
     * @throws ResetPasswordTokenExpired
     * @throws InvalidPassword
     * @throws InvalidUser
     *
     * @Mutation
     */
    public function updatePassword(
        string $resetPasswordTokenId,
        string $plainToken,
        string $newPassword
    ): bool {
        $this->update(
            $resetPasswordTokenId,
            $plainToken,
            $newPassword
        );

        // Do not return any relevant information
        // of the user as this is not a secure endpoint.
        return true;
    }

    /**
     * @throws ResetPasswordTokenNotFoundById
     * @throws WrongResetPasswordToken
     * @throws ResetPasswordTokenExpired
     * @throws InvalidPassword
     * @throws InvalidUser
     */
    public function update(
        string $resetPasswordTokenId,
        string $plainToken,
        string $newPassword
    ): User {
        $resetPasswordToken = $this->resetPasswordTokenDao->mustFindOneById($resetPasswordTokenId);

        $token = $resetPasswordToken->getToken();
        if (! password_verify($plainToken, $token)) {
            throw new WrongResetPasswordToken();
        }

        $now = new DateTimeImmutable();
        if ($now > $resetPasswordToken->getValidUntil()) {
            throw new ResetPasswordTokenExpired();
        }

        $passwordProxy = new PasswordProxy($newPassword);
        $user          = $resetPasswordToken->getUser();

        $this->userDao->updatePassword($user, $passwordProxy);
        $this->resetPasswordTokenDao->delete($resetPasswordToken);

        return $user;
    }
}
