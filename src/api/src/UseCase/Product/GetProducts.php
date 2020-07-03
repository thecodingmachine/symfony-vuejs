<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Enum\Filter\ProductsSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Model\Product;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetProducts
{
    private ProductDao $productDao;

    public function __construct(ProductDao $productDao)
    {
        $this->productDao = $productDao;
    }

    /**
     * @return Product[]|ResultIterator
     *
     * @Query
     */
    public function products(
        ?string $search = null,
        ?float $lowerPrice = null,
        ?float $upperPrice = null,
        ?ProductsSortBy $sortBy = null,
        ?SortOrder $sortOrder = null
    ): ResultIterator {
        return $this->productDao->search(
            $search,
            $lowerPrice,
            $upperPrice,
            $sortBy,
            $sortOrder
        );
    }
}
