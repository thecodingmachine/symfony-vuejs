<?php

declare(strict_types=1);

use App\Domain\Enum\Filter\ProductsSortByEnum;
use App\Domain\Enum\Filter\SortOrderEnum;
use App\Domain\Model\Product;
use App\Domain\Throwable\Invalid\InvalidProductsFilters;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\SearchProducts;

beforeEach(function (): void {
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);
    $createProduct = self::$container->get(CreateProduct::class);
    assert($createProduct instanceof CreateProduct);

    $company = $createCompany->create(
        'a',
        'http://a.a'
    );

    $createProduct->create(
        'a',
        10,
        $company->getId()
    );

    $createProduct->create(
        'b',
        200,
        $company->getId()
    );

    $createProduct->create(
        'c',
        3000,
        $company->getId()
    );
});

it(
    'finds all products',
    function (): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $result = $searchProducts->searchProducts();
        assertCount(3, $result);
    }
);

it(
    'filters products with a generic search',
    function (string $search): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $result = $searchProducts->searchProducts($search);
        assertCount(1, $result);

        $product = $result->first();
        assert($product instanceof Product);
        assertStringContainsStringIgnoringCase($search, $product->getName());
    }
)
    ->with(['a', 'b', 'c']);

it(
    'filters products by price range',
    function (?float $lowerPrice, ?float $upperPrice): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $result = $searchProducts->searchProducts(null, $lowerPrice, $upperPrice);

        if ($lowerPrice === null && $upperPrice === null) {
            assertCount(3, $result);

            return;
        }

        if ($lowerPrice !== null && $upperPrice !== null && $lowerPrice > $upperPrice) {
            assertCount(0, $result);

            return;
        }

        /** @var Product[] $products */
        $products = $result->toArray();

        foreach ($products as $product) {
            if ($lowerPrice !== null) {
                assertGreaterThanOrEqual($lowerPrice, $product->getPrice());
            }

            if ($upperPrice === null) {
                continue;
            }

            assertLessThanOrEqual($upperPrice, $product->getPrice());
        }
    }
)
    ->with([
        [null, null],
        [10, null],
        [null, 10],
        [100, 300],
        [2000, 10000],
        [1000, 200],
    ]);

it(
    'sorts products by name',
    function (string $sortOrder): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $result = $searchProducts->searchProducts(
            null,
            null,
            null,
            ProductsSortByEnum::NAME,
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Product[] $products */
        $products = $result->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
            assertStringContainsStringIgnoringCase('a', $products[0]->getName());
            assertStringContainsStringIgnoringCase('b', $products[1]->getName());
            assertStringContainsStringIgnoringCase('c', $products[2]->getName());
        } else {
            assertStringContainsStringIgnoringCase('a', $products[2]->getName());
            assertStringContainsStringIgnoringCase('b', $products[1]->getName());
            assertStringContainsStringIgnoringCase('c', $products[0]->getName());
        }
    }
)
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'sorts products by price',
    function (string $sortOrder): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $result = $searchProducts->searchProducts(
            null,
            null,
            null,
            ProductsSortByEnum::PRICE,
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Product[] $products */
        $products = $result->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
            assertTrue(
                $products[0]->getPrice() <
                $products[1]->getPrice() &&
                $products[1]->getPrice() <
                $products[2]->getPrice()
            );
        } else {
            assertTrue(
                $products[0]->getPrice() >
                $products[1]->getPrice() &&
                $products[1]->getPrice() >
                $products[2]->getPrice()
            );
        }
    }
)
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'throws an exception if invalid filters',
    function (string $sortBy, string $sortOrder): void {
        $searchProducts = self::$container->get(SearchProducts::class);
        assert($searchProducts instanceof SearchProducts);

        $searchProducts->searchProducts(
            null,
            null,
            null,
            $sortBy,
            $sortOrder
        );
    }
)
    ->with([
        // Invalid sort by.
        ['foo', SortOrderEnum::ASC],
        // Invalid sort order.
        [ProductsSortByEnum::NAME, 'foo'],
    ])
    ->throws(InvalidProductsFilters::class);
