<?php

declare(strict_types=1);

namespace App\UseCase\Product;

use App\Domain\Dao\ProductDao;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class CheckProductNameAvailability
{
    private ProductDao $productDao;

    public function __construct(ProductDao $productDao)
    {
        $this->productDao = $productDao;
    }

    /**
     * @Query
     */
    public function checkProductNameAvailability(string $name): bool
    {
        // TODO add @Right("ROLE_COMPANY")
        try {
            $this->productDao->mustNotFindOneByName($name);

            return true;
        } catch (ProductWithNameExists $e) {
            return false;
        }
    }
}
