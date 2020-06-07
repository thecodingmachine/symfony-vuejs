<?php

declare(strict_types=1);

namespace App\Domain\Model\Filter;

use App\Domain\Enum\Filter\CompaniesSortByEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class CompaniesFilters extends Filters
{
    private ?string $search;

    public function __construct(
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ) {
        $this->search = $search;
        parent::__construct($sortBy, $sortOrder);
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @Assert\Choice(callback={"App\Domain\Enum\Filter\CompaniesSortByEnum", "values"})
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    protected function getDefaultSortBy(): string
    {
        return CompaniesSortByEnum::NAME;
    }
}
