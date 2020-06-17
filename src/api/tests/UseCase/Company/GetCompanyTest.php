<?php

declare(strict_types=1);

use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\GetCompany;

it(
    'gets a company',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $getCompany    = self::$container->get(GetCompany::class);
        assert($createCompany instanceof CreateCompany);
        assert($getCompany instanceof GetCompany);

        $company = $createCompany->create('foo');

        $foundCompany = $getCompany->getCompanyById($company->getId());
        assertEquals($company->getId(), $foundCompany->getId());
    }
);

it(
    'throws an exception if invalid id.',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $getCompany    = self::$container->get(GetCompany::class);
        assert($createCompany instanceof CreateCompany);
        assert($getCompany instanceof GetCompany);

        $createCompany->create('foo');
        $getCompany->getCompanyById('foo');
    }
)
    ->throws(CompanyNotFoundById::class);
