<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Model\Product;
use App\Domain\Throwable\NotFound\ProductNotFoundById;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetProduct
{
    private ProductDao $productDao;

    public function __construct(ProductDao $productDao)
    {
        $this->productDao = $productDao;
    }

    /**
     * @throws ProductNotFoundById
     *
     * @Query
     */
    public function getProductById(string $id): Product
    {
        return $this->productDao->mustFindOneById($id);
    }
}
