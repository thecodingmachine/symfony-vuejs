<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\Product;
use App\Domain\Model\User;
use App\Domain\Storage\OrderInvoiceStorage;
use App\Domain\Throwable\InvalidModel;
use App\UseCase\Order\CreateOrder;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);
    $productDao = self::$container->get(ProductDao::class);
    assert($productDao instanceof ProductDao);

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
        'bar',
        1
    );
    $product->setId('1');
    $productDao->save($product);
});

it(
    'creates an order',
    function (
        int $quantity
    ): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $createOrder = self::$container->get(CreateOrder::class);
        assert($createOrder instanceof CreateOrder);
        $orderInvoiceStorage = self::$container->get(OrderInvoiceStorage::class);
        assert($orderInvoiceStorage instanceof OrderInvoiceStorage);

        $user    = $userDao->getById('1');
        $product = $productDao->getById('1');
        $order   = $createOrder->createOrder(
            $user,
            $product,
            $quantity
        );

        assertEquals($user, $order->getUser());
        assertEquals($product, $order->getProduct());
        assertEquals($quantity, $order->getQuantity());
        assertEquals($product->getPrice(), $order->getUnitPrice());
        assertEquals($quantity * $order->getUnitPrice(), $order->getTotal());
        assertNotEquals('tmp', $order->getInvoice());
        assertTrue($orderInvoiceStorage->fileExists($order->getInvoice()));
    }
)
    ->with([
        1,
        5,
        100,
    ])
    ->group('order');

it(
    'throws an exception if invalid order',
    function (
        int $quantity
    ): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $createOrder = self::$container->get(CreateOrder::class);
        assert($createOrder instanceof CreateOrder);
        $orderInvoiceStorage = self::$container->get(OrderInvoiceStorage::class);
        assert($orderInvoiceStorage instanceof OrderInvoiceStorage);

        $user    = $userDao->getById('1');
        $product = $productDao->getById('1');
        $createOrder->createOrder(
            $user,
            $product,
            $quantity
        );
    }
)
    ->with([
        // 0 quantity.
        0,
        // Negative quantity.
        -10,
    ])
    ->throws(InvalidModel::class)
    ->group('order');
