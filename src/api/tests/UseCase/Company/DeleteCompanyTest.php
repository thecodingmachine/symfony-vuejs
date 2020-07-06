<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\DeleteCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\User\CreateUser;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);

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
    $company->setId('1');
    $companyDao->save($company);
});

it(
    'deletes the company',
    function (): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        assert($deleteCompany instanceof DeleteCompany);

        $company = $companyDao->getById('1');
        $deleteCompany->deleteCompany($company);

        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('company');

it(
    "deletes company's products",
    function (): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        assert($deleteCompany instanceof DeleteCompany);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);

        $company = $companyDao->getById('1');
        $product = $createProduct->create(
            'foo',
            1,
            $company
        );

        $deleteCompany->deleteCompany($company);
        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('company');
