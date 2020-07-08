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
use App\UseCase\Product\GetProduct;

it(
    'gets a product',
    function (): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $getProduct = self::$container->get(GetProduct::class);
        assert($getProduct instanceof GetProduct);

        $merchant = new User(
            'foo',
            'bar',
            'merchant@foo.com',
            strval(Locale::EN()),
            strval(Role::MERCHANT())
        );
        $userDao->save($merchant);

        $company = new Company(
            $merchant,
            'foo'
        );
        $companyDao->save($company);

        $product = new Product(
            $company,
            'foo',
            1
        );
        $productDao->save($product);

        $foundProduct = $getProduct->product($product);
        assertEquals($product, $foundProduct);
    }
)
    ->group('product');
