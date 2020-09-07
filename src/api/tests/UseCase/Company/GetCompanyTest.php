<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\UseCase\Company\GetCompany;

use function PHPUnit\Framework\assertEquals;

it(
    'gets a company',
    function (): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $getCompany = self::$container->get(GetCompany::class);
        assert($getCompany instanceof GetCompany);

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

        $foundCompany = $getCompany->company($company);
        assertEquals($company, $foundCompany);
    }
)
    ->group('company');
