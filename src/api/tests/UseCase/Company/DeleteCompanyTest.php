<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Storage\CompanyLogoStorage;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\DeleteCompany;
use App\UseCase\Product\CreateProduct;
use TheCodingMachine\TDBM\TDBMException;

it(
    'deletes the company',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        $companyDao    = self::$container->get(CompanyDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($deleteCompany instanceof DeleteCompany);
        assert($companyDao instanceof CompanyDao);

        $company = $createCompany->create('foo');
        $createProduct->create(
            'foo',
            1,
            $company,
            null
        );

        $deleteCompany->deleteCompany($company);
        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class);

it(
    'deletes the logo',
    function (): void {
        $createCompany      = self::$container->get(CreateCompany::class);
        $createProduct      = self::$container->get(CreateProduct::class);
        $companyLogoStorage = self::$container->get(CompanyLogoStorage::class);
        $deleteCompany      = self::$container->get(DeleteCompany::class);
        $companyDao         = self::$container->get(CompanyDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($companyLogoStorage instanceof  CompanyLogoStorage);
        assert($deleteCompany instanceof DeleteCompany);
        assert($companyDao instanceof CompanyDao);

        $storable = CompanyLogo::createFromPath(
            dirname(__FILE__) . '/foo.jpg',
        );

        $company = $createCompany->create(
            'foo',
            null,
            $storable
        );

        $deleteCompany->deleteCompany($company);

        assertFalse($companyLogoStorage->fileExists($storable->getFilename()));
        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class);
