<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\SearchUsers;
use App\Domain\Model\User;
use App\Domain\Repository\Filter\User\InvalidUsersFilters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\TDBM\ResultIterator;

final class SearchUsersController extends AbstractController
{
    private SearchUsers $searchUsers;

    public function __construct(SearchUsers $searchUsers)
    {
        $this->searchUsers = $searchUsers;
    }

    /**
     * @return User[]|ResultIterator
     *
     * @throws InvalidUsersFilters
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function searchUsers(
        ?string $search = null,
        ?string $role = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ) : ResultIterator {
        return $this->searchUsers->search(
            $search,
            $role,
            $sortBy,
            $sortOrder
        );
    }
}
