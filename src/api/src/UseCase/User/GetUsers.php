<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Model\Filter\UsersFilters;
use App\Domain\Model\User;
use App\Domain\Throwable\Invalid\InvalidUsersFilters;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\TDBM\ResultIterator;

final class GetUsers
{
    private UserDao $userDao;

    public function __construct(UserDao $userDao)
    {
        $this->userDao = $userDao;
    }

    /**
     * @return User[]|ResultIterator
     *
     * @throws InvalidUsersFilters
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function users(
        ?string $search = null,
        ?string $role = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ): ResultIterator {
        $filters = new UsersFilters($search, $role, $sortBy, $sortOrder);

        return $this->userDao->search($filters);
    }
}
