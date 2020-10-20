<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use App\UseCase\User\ResetPassword\ResetPassword;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class CreateUser
{
    private UserDao $userDao;
    private ResetPassword $resetPassword;

    public function __construct(
        UserDao $userDao,
        ResetPassword $resetPassword
    ) {
        $this->userDao       = $userDao;
        $this->resetPassword = $resetPassword;
    }

    /**
     * @throws InvalidModel
     *
     * @Mutation
     * @Logged
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ): User {
        $user = new User(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $this->userDao->save($user);
        $this->resetPassword->resetPassword($email);

        return $user;
    }
}
