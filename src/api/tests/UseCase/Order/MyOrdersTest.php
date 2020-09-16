<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\OrderDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Filter\OrdersSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\Order;
use App\Domain\Model\Product;
use App\Domain\Model\User;
use App\UseCase\Order\MyOrders;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;
use function PHPUnit\Framework\assertTrue;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);
    $productDao = self::$container->get(ProductDao::class);
    assert($productDao instanceof ProductDao);
    $orderDao = self::$container->get(OrderDao::class);
    assert($orderDao instanceof OrderDao);

    $merchant = new User(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );
    $userDao->save($merchant);

    $client = new User(
        'foo',
        'bar',
        'client@foo.com',
        Locale::EN(),
        Role::CLIENT()
    );
    $client->setId('1');
    $userDao->save($client);

    $company = new Company(
        $merchant,
        'foo'
    );
    $companyDao->save($company);

    $product = new Product(
        $company,
        'a',
        10
    );
    $productDao->save($product);

    $order = new Order(
        $client,
        $product,
        10
    );
    $orderDao->save($order);

    $product = new Product(
        $company,
        'b',
        100
    );
    $productDao->save($product);

    $order = new Order(
        $client,
        $product,
        100
    );
    $orderDao->save($order);

    $product = new Product(
        $company,
        'c',
        1000
    );
    $productDao->save($product);

    $order = new Order(
        $client,
        $product,
        1000
    );
    $orderDao->save($order);
});

it(
    'finds all orders',
    function (): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders($user);
        assertCount(3, $result);
    }
)
    ->group('order');

it(
    'filters orders with a generic search',
    function (string $search): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders($user, $search);
        assertCount(1, $result);

        $order = $result->first();
        assert($order instanceof Order);
        assertStringContainsStringIgnoringCase($search, $order->getProduct()->getName());
    }
)
    ->with(['a', 'b', 'c'])
    ->group('order');

it(
    'sorts orders by product name',
    function (SortOrder $sortOrder): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders(
            $user,
            null,
            OrdersSortBy::PRODUCT_NAME(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Order[] $orders */
        $orders = $result->toArray();
        if ($sortOrder->equals(SortOrder::ASC())) {
            assertStringContainsStringIgnoringCase('a', $orders[0]->getProduct()->getName());
            assertStringContainsStringIgnoringCase('b', $orders[1]->getProduct()->getName());
            assertStringContainsStringIgnoringCase('c', $orders[2]->getProduct()->getName());
        } else {
            assertStringContainsStringIgnoringCase('a', $orders[2]->getProduct()->getName());
            assertStringContainsStringIgnoringCase('b', $orders[1]->getProduct()->getName());
            assertStringContainsStringIgnoringCase('c', $orders[0]->getProduct()->getName());
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('order');

it(
    'sorts orders by unit price',
    function (SortOrder $sortOrder): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders(
            $user,
            null,
            OrdersSortBy::UNIT_PRICE(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Order[] $orders */
        $orders = $result->toArray();
        if ($sortOrder->equals(SortOrder::ASC())) {
            assertTrue(
                $orders[0]->getUnitPrice() <
                $orders[1]->getUnitPrice() &&
                $orders[1]->getUnitPrice() <
                $orders[2]->getUnitPrice()
            );
        } else {
            assertTrue(
                $orders[0]->getUnitPrice() >
                $orders[1]->getUnitPrice() &&
                $orders[1]->getUnitPrice() >
                $orders[2]->getUnitPrice()
            );
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('order');

it(
    'sorts orders by quantity',
    function (SortOrder $sortOrder): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders(
            $user,
            null,
            OrdersSortBy::QUANTITY(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Order[] $orders */
        $orders = $result->toArray();
        if ($sortOrder->equals(SortOrder::ASC())) {
            assertTrue(
                $orders[0]->getQuantity() <
                $orders[1]->getQuantity() &&
                $orders[1]->getQuantity() <
                $orders[2]->getQuantity()
            );
        } else {
            assertTrue(
                $orders[0]->getQuantity() >
                $orders[1]->getQuantity() &&
                $orders[1]->getQuantity() >
                $orders[2]->getQuantity()
            );
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('order');

it(
    'sorts orders by total',
    function (SortOrder $sortOrder): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $myOrders = self::$container->get(MyOrders::class);
        assert($myOrders instanceof MyOrders);

        $user = $userDao->getById('1');

        $result = $myOrders->myOrders(
            $user,
            null,
            OrdersSortBy::TOTAL(),
            $sortOrder
        );
        assertCount(3, $result);

        /** @var Order[] $orders */
        $orders = $result->toArray();
        if ($sortOrder->equals(SortOrder::ASC())) {
            assertTrue(
                $orders[0]->getTotal() <
                $orders[1]->getTotal() &&
                $orders[1]->getTotal() <
                $orders[2]->getTotal()
            );
        } else {
            assertTrue(
                $orders[0]->getTotal() >
                $orders[1]->getTotal() &&
                $orders[1]->getTotal() >
                $orders[2]->getTotal()
            );
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('order');
