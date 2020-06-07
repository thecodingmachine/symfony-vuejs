<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Product;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\NotFound\ProductNotFoundById;

interface ProductRepository
{
    /**
     * @throws InvalidProduct
     */
    public function save(Product $product): void;

    /**
     * @throws ProductNotFoundById
     */
    public function mustFindOneById(string $id): Product;

    /**
     * @throws ProductWithNameExists
     */
    public function mustNotFindOneByName(string $name): void;
}
