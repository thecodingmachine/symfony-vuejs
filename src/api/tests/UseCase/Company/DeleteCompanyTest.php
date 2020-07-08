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
use App\UseCase\Company\DeleteCompany;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);

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
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        assert($deleteCompany instanceof DeleteCompany);

        $company = $companyDao->getById('1');
        $product = new Product(
            $company,
            'foo',
            1
        );
        $productDao->save($product);

        $deleteCompany->deleteCompany($company);
        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('company');
