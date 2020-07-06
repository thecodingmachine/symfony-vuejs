<?php

declare(strict_types=1);

use App\Domain\Enum\Filter\ProductsSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Product;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\GetProducts;
use App\UseCase\User\CreateUser;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);
    $createProduct = self::$container->get(CreateProduct::class);
    assert($createProduct instanceof CreateProduct);

    $merchant = $createUser->createUser(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );

    $company = $createCompany->createCompany(
        $merchant,
        'a',
        'http://a.a'
    );

    $createProduct->create(
        'a',
        10,
        $company
    );

    $createProduct->create(
        'b',
        200,
        $company
    );

    $createProduct->create(
        'c',
        3000,
        $company
    );
});

it(
    'finds all products',
    function (): void {
        $getProducts = self::$container->get(GetProducts::class);
        assert($getProducts instanceof GetProducts);

        $result = $getProducts->products();
        assertCount(3, $result);
    }
)
    ->group('product');

it(
    'filters products with a generic search',
    function (string $search): void {
        $getProducts = self::$container->get(GetProducts::class);
        assert($getProducts instanceof GetProducts);

        $result = $getProducts->products($search);
        assertCount(1, $result);

        $product = $result->first();
        assert($product instanceof Product);
        assertStringContainsStringIgnoringCase($search, $product->getName());
    }
)
    ->with(['a', 'b', 'c'])
    ->group('product');

it(
    'filters products by price range',
    function (?float $lowerPrice, ?float $upperPrice): void {
        $getProducts = self::$container->get(GetProducts::class);
        assert($getProducts instanceof GetProducts);

        $result = $getProducts->products(null, $lowerPrice, $upperPrice);

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
    ])
    ->group('product');

it(
    'sorts products by name',
    function (SortOrder $sortOrder): void {
        $getProducts = self::$container->get(GetProducts::class);
        assert($getProducts instanceof GetProducts);

        $result = $getProducts->products(
            null,
            null,
            null,
            ProductsSortBy::NAME(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Product[] $products */
        $products = $result->toArray();
        if ($sortOrder === SortOrder::ASC()) {
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
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('product');

it(
    'sorts products by price',
    function (SortOrder $sortOrder): void {
        $getProducts = self::$container->get(GetProducts::class);
        assert($getProducts instanceof GetProducts);

        $result = $getProducts->products(
            null,
            null,
            null,
            ProductsSortBy::PRICE(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Product[] $products */
        $products = $result->toArray();
        if ($sortOrder === SortOrder::ASC()) {
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
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('product');
