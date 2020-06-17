<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Model\User;
use App\Domain\Throwable\Exists\UserWithEmailExists;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\UseCase\User\ResetPassword\ResetPassword;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

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
     * @throws UserWithEmailExists
     * @throws InvalidUser
     * @throws UserNotFoundByEmail
     *
     * @Mutation
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ): User {
        $this->userDao->mustNotFindOneByEmail($email);

        $user = new User(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $this->userDao->save($user);
        $this->resetPassword->reset($email);

        return $user;
    }
}
