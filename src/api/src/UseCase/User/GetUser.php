<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Model\User;
use App\Domain\Throwable\NotFound\UserNotFoundById;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetUser
{
    private UserDao $userDao;

    public function __construct(UserDao $userDao)
    {
        $this->userDao = $userDao;
    }

    /**
     * @throws UserNotFoundById
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function getUserById(string $id): User
    {
        return $this->userDao->mustFindOneById($id);
    }
}
