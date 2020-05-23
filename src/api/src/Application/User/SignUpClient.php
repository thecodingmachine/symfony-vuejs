<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\User\CreateUser\CreateUser;
use App\Application\User\CreateUser\InvalidUser;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;

final class SignUpClient
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @throws UserWithEmailExist
     * @throws InvalidUser
     * @throws UserNotFoundByEmail
     */
    public function signUp(
        string $firstName,
        string $lastName,
        string $email
    ) : User {
        return $this
            ->createUser
            ->create(
                $firstName,
                $lastName,
                $email,
                RoleEnum::CLIENT
            );
    }
}
