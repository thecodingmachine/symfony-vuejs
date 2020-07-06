<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\GetCompany;
use App\UseCase\User\CreateUser;

it(
    'gets a company',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $getCompany = self::$container->get(GetCompany::class);
        assert($getCompany instanceof GetCompany);

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

        $foundCompany = $getCompany->company($company);
        assertEquals($company, $foundCompany);
    }
)
    ->group('company');
