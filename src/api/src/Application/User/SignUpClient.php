<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Throwable\Exists\UserWithEmailExists;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;

final class SignUpClient
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @throws UserWithEmailExists
     * @throws InvalidUser
     * @throws UserNotFoundByEmail
     */
    public function signUp(
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ): User {
        return $this
            ->createUser
            ->create(
                $firstName,
                $lastName,
                $email,
                $locale,
                RoleEnum::CLIENT
            );
    }
}
