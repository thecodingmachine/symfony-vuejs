<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use Safe\DateTimeImmutable;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function password_verify;

final class UpdatePassword
{
    private ValidatorInterface $validator;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private UserRepository $userRepository;

    public function __construct(
        ValidatorInterface $validator,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        UserRepository $userRepository
    ) {
        $this->validator                    = $validator;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->userRepository               = $userRepository;
    }

    /**
     * @throws ResetPasswordTokenNotFoundById
     * @throws WrongResetPasswordToken
     * @throws ResetPasswordTokenExpired
     * @throws InvalidPassword
     */
    public function update(
        string $resetPasswordTokenId,
        string $plainToken,
        string $newPassword
    ) : void {
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
        $violations    = $this->validator->validate($passwordProxy);
        InvalidPassword::throwException($violations);

        $user = $resetPasswordToken->getUser();
        $user->setPassword($passwordProxy->getPlainPassword());

        $this->userRepository->save($user);
        $this->resetPasswordTokenRepository->delete($resetPasswordToken);
    }
}
