<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use App\Tests\UseCase\DummyValues;
use App\UseCase\Company\UpdateCompany;

use function PHPUnit\Framework\assertEquals;

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

    $company = new Company(
        $merchant,
        'bar'
    );
    $companyDao->save($company);
});

it(
    'updates a company',
    function (
        string $name,
        ?string $website
    ): void {
        $updateCompany = self::$container->get(UpdateCompany::class);
        assert($updateCompany instanceof UpdateCompany);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);

        $company = $updateCompany->updateCompany(
            $companyDao->getById('1'),
            $name,
            $website
        );

        assertEquals($name, $company->getName());
        assertEquals($website, $company->getWebsite());
    }
)
    ->with([
        ['foo', null],
        ['foo', 'http://foo.bar'],
    ])
    ->group('company');

it(
    'throws an exception if invalid company',
    function (
        string $name,
        ?string $website
    ): void {
        $updateCompany = self::$container->get(UpdateCompany::class);
        assert($updateCompany instanceof UpdateCompany);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);

        $updateCompany->updateCompany(
            $companyDao->getById('1'),
            $name,
            $website
        );
    }
)
    ->with([
        // Existing name.
        ['bar', null],
        // Blank name.
        [DummyValues::BLANK, null],
        // Name > 255.
        [DummyValues::CHAR256, null],
        // Blank website.
        ['foo', DummyValues::BLANK],
        // Website > 255.
        ['foo', DummyValues::CHAR256],
        // Website is not a URL.
        ['foo', 'foo'],
        ['foo', 'foo.bar'],
    ])
    ->throws(InvalidModel::class)
    ->group('company');
