<?php

declare(strict_types=1);

namespace App\Domain\Repository\Filter\User;

use App\Domain\Repository\Filter\Filters;
use Symfony\Component\Validator\Constraints as Assert;

final class UsersFilters extends Filters
{
    private ?string $search;
    /** @Assert\Choice(callback={"App\Domain\Enum\RoleEnum", "values"}) */
    private ?string $role;

    public function __construct(
        ?string $search = null,
        ?string $role = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ) {
        $this->search = $search;
        $this->role   = $role;
        parent::__construct($sortBy, $sortOrder);
    }

    public function getSearch() : ?string
    {
        return $this->search;
    }

    public function getRole() : ?string
    {
        return $this->role;
    }

    /**
     * @Assert\Choice(callback={"App\Domain\Repository\Filter\User\UsersSortByEnum", "values"})
     */
    public function getSortBy() : string
    {
        return $this->sortBy;
    }

    protected function getDefaultSortBy() : string
    {
        return UsersSortByEnum::FIRST_NAME;
    }
}
