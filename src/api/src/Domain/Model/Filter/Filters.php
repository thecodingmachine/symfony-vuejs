<?php

declare(strict_types=1);

namespace App\Domain\Model\Filter;

use App\Domain\Enum\Filter\SortOrderEnum;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Filters
{
    protected string $sortBy;
    /** @Assert\Choice(callback={"App\Domain\Enum\Filter\SortOrderEnum", "values"}) */
    private string $sortOrder;

    public function __construct(?string $sortBy = null, ?string $sortOrder = null)
    {
        $this->sortBy    = $sortBy ?: $this->getDefaultSortBy();
        $this->sortOrder = $sortOrder ?: $this->getDefaultSortOrder();
    }

    abstract public function getSortBy() : string;

    abstract protected function getDefaultSortBy() : string;

    public function getSortOrder() : string
    {
        return $this->sortOrder;
    }

    protected function getDefaultSortOrder() : string
    {
        return SortOrderEnum::ASC;
    }
}
