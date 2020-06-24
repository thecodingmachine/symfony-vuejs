<?php

declare(strict_types=1);

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

        $foundCompany = $getCompany->company($company);
        assertEquals($company->getId(), $foundCompany->getId());
    }
);
