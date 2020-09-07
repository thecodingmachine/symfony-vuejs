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
use App\UseCase\Company\CreateCompany;

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
    $merchant->setId('1');
    $userDao->save($merchant);

    $client = new User(
        'foo',
        'bar',
        'client@foo.com',
        strval(Locale::EN()),
        strval(Role::CLIENT())
    );
    $client->setId('2');
    $userDao->save($client);

    $company = new Company(
        $merchant,
        'bar'
    );
    $companyDao->save($company);
});

it(
    'creates a company',
    function (
        string $name,
        ?string $website
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);

        $company = $createCompany
            ->createCompany(
                $userDao->getById('1'),
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
        string $userId,
        string $name,
        ?string $website
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);

        $createCompany->createCompany(
            $userDao->getById($userId),
            $name,
            $website
        );
    }
)
    ->with([
        // Existing name.
        ['1', 'bar', null],
        // Blank name.
        ['1', DummyValues::BLANK, null],
        // Name > 255.
        ['1', DummyValues::CHAR256, null],
        // Blank website.
        ['1', 'foo', DummyValues::BLANK],
        // Website > 255.
        ['1', 'foo', DummyValues::CHAR256],
        // Website is not a URL.
        ['1', 'foo', 'foo'],
        ['1', 'foo', 'foo.bar'],
        // User is not a merchant.
        ['2', 'foo', null],
    ])
    ->throws(InvalidModel::class)
    ->group('company');
