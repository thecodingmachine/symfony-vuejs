<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Filter\UsersSortBy;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Security;
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
     * @Query
     * @Logged
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     */
    public function users(
        ?string $search = null,
        ?Role $role = null,
        ?UsersSortBy $sortBy = null,
        ?SortOrder $sortOrder = null
    ): ResultIterator {
        return $this->userDao->search(
            $search,
            $role,
            $sortBy,
            $sortOrder
        );
    }
}
