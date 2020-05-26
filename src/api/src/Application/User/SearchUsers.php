<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;
use App\Domain\Repository\Search\User\InvalidUsersFilters;
use App\Domain\Repository\Search\User\UsersFilters;
use App\Domain\Repository\UserRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class SearchUsers
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return User[]|ResultIterator
     *
     * @throws InvalidUsersFilters
     */
    public function search(
        ?string $search = null,
        ?string $role = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ) : ResultIterator {
        $filters = new UsersFilters($search, $role, $sortBy, $sortOrder);

        return $this->userRepository->search($filters);
    }
}
