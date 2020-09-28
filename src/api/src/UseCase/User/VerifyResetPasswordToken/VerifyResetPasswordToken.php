<?php

declare(strict_types=1);

namespace App\UseCase\User\VerifyResetPasswordToken;

use App\Domain\Dao\ResetPasswordTokenDao;
use Safe\DateTimeImmutable;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\TDBM\NoBeanFoundException;

use function password_verify;

final class VerifyResetPasswordToken
{
    private ResetPasswordTokenDao $resetPasswordTokenDao;

    public function __construct(ResetPasswordTokenDao $resetPasswordTokenDao)
    {
        $this->resetPasswordTokenDao = $resetPasswordTokenDao;
    }

    /**
     * @throws InvalidResetPasswordTokenId
     * @throws WrongResetPasswordToken
     * @throws ResetPasswordTokenExpired
     *
     * @Mutation
     */
    public function verifyResetPasswordToken(
        string $resetPasswordTokenId,
        string $plainToken
    ): bool {
        try {
            $resetPasswordToken = $this->resetPasswordTokenDao->getById($resetPasswordTokenId);
        } catch (NoBeanFoundException $e) {
            throw new InvalidResetPasswordTokenId($e);
        }

        $token = $resetPasswordToken->getToken();
        if (! password_verify($plainToken, $token)) {
            throw new WrongResetPasswordToken();
        }

        $now = new DateTimeImmutable();
        if ($now > $resetPasswordToken->getValidUntil()) {
            throw new ResetPasswordTokenExpired();
        }

        return true;
    }
}
