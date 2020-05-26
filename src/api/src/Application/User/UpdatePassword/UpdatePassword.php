<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use App\Domain\Model\Proxy\PasswordProxy;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use Safe\DateTimeImmutable;
use function password_verify;

final class UpdatePassword
{
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private UserRepository $userRepository;

    public function __construct(
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        UserRepository $userRepository
    ) {
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->userRepository               = $userRepository;
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
    ) : User {
        $resetPasswordToken = $this->resetPasswordTokenRepository->mustFindOneById($resetPasswordTokenId);

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

        $this->userRepository->updatePassword($user, $passwordProxy);
        $this->resetPasswordTokenRepository->delete($resetPasswordToken);

        return $user;
    }
}
