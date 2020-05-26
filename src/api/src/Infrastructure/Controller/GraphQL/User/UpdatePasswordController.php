<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\UpdatePassword\ResetPasswordTokenExpired;
use App\Application\User\UpdatePassword\UpdatePassword;
use App\Application\User\UpdatePassword\WrongResetPasswordToken;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdatePasswordController extends AbstractController
{
    private UpdatePassword $updatePassword;

    public function __construct(UpdatePassword $updatePassword)
    {
        $this->updatePassword = $updatePassword;
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
    ) : bool {
         $this->updatePassword->update(
             $resetPasswordTokenId,
             $plainToken,
             $newPassword
         );

         return true;
    }
}
