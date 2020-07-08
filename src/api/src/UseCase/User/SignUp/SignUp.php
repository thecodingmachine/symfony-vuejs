<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUp;

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Throwable\InvalidModel;
use App\UseCase\User\CreateUser;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class SignUp
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @throws WrongRole
     * @throws InvalidModel
     *
     * @Mutation
     */
    public function signUp(
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ): bool {
        if ($role->equals(Role::ADMINISTRATOR())) {
            throw new WrongRole();
        }

        $this->createUser->createUser(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        return true;
    }
}
