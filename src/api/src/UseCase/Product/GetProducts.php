<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Filter\ProductsFilters;
use App\Domain\Model\Product;
use App\Domain\Throwable\Invalid\InvalidProductsFilters;
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
     * @throws InvalidProductsFilters
     *
     * @Query
     */
    public function products(
        ?string $search = null,
        ?float $lowerPrice = null,
        ?float $upperPrice = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ): ResultIterator {
        $filters = new ProductsFilters(
            $search,
            $lowerPrice,
            $upperPrice,
            $sortBy,
            $sortOrder
        );

        return $this->productDao->search($filters);
    }
}
