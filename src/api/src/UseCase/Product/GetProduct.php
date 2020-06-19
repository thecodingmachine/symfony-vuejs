<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Model\Product;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetProduct
{
    /**
     * @Query
     */
    public function getProductById(Product $product): Product
    {
        return $product;
    }
}
