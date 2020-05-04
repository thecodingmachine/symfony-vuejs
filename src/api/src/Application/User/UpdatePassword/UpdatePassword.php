<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
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
     * @throws InvalidResetPasswordToken
     * @throws ResetPasswordTokenExpired
     */
    public function update(
        string $resetPasswordTokenId,
        string $plainToken,
        string $newPassword
    ) : void {
        $resetPasswordToken = $this->resetPasswordTokenRepository->mustFindOneById($resetPasswordTokenId);

        // Token not valid.
        $token = $resetPasswordToken->getToken();
        if (! password_verify($plainToken, $token)) {
            throw new InvalidResetPasswordToken();
        }

        // Token expired.
        $now = new DateTimeImmutable();
        if ($now > $resetPasswordToken->getValidUntil()) {
            throw new ResetPasswordTokenExpired();
        }

        $user = $resetPasswordToken->getUser();
        $user->setPassword($newPassword);
        $this->userRepository->save($user);

        $this->resetPasswordTokenRepository->delete($resetPasswordToken);
    }
}
