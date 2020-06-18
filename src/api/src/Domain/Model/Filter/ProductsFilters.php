<?php

declare(strict_types=1);

namespace App\Domain\Model\Filter;

use App\Domain\Enum\Filter\ProductsSortByEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductsFilters extends Filters
{
    private ?string $search;
    private ?float $lowerPrice;
    private ?float $upperPrice;

    public function __construct(
        ?string $search = null,
        ?float $lowerPrice = null,
        ?float $upperPrice = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ) {
        $this->search     = $search;
        $this->lowerPrice = $lowerPrice;
        $this->upperPrice = $upperPrice;
        parent::__construct($sortBy, $sortOrder);
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getLowerPrice(): ?float
    {
        return $this->lowerPrice;
    }

    public function getUpperPrice(): ?float
    {
        return $this->upperPrice;
    }

    /**
     * @Assert\Choice(callback={"App\Domain\Enum\Filter\ProductsSortByEnum", "values"})
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    protected function getDefaultSortBy(): string
    {
        return ProductsSortByEnum::PRICE;
    }
}
