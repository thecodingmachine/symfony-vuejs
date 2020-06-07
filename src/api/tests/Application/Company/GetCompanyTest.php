<?php

declare(strict_types=1);

use App\Application\Company\CreateCompany;
use App\Application\Company\GetCompany;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;

it(
    'gets a company',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $getCompany    = self::$container->get(GetCompany::class);
        assert($createCompany instanceof CreateCompany);
        assert($getCompany instanceof GetCompany);

        $company = $createCompany->create('foo');

        $foundCompany = $getCompany->byId($company->getId());
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

        $company = $createCompany->create('foo');

        $getCompany->byId('foo');
    }
)
    ->throws(CompanyNotFoundById::class);
