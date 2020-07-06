<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\GetProduct;
use App\UseCase\User\CreateUser;

it(
    'gets a product',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $getProduct = self::$container->get(GetProduct::class);
        assert($getProduct instanceof GetProduct);

        $merchant = $createUser->createUser(
            'foo',
            'bar',
            'merchant@foo.com',
            Locale::EN(),
            Role::MERCHANT()
        );

        $company = $createCompany->createCompany(
            $merchant,
            'foo'
        );
        $product = $createProduct->create('foo', 1, $company);

        $foundProduct = $getProduct->product($product);
        assertEquals($product, $foundProduct);
    }
)
    ->group('product');
