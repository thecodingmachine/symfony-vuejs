<?php

declare(strict_types=1);

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Throwable\InvalidModel;
use App\Tests\UseCase\DummyValues;
use App\UseCase\Company\CreateCompany;
use App\UseCase\User\CreateUser;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);

    $merchant = $createUser->createUser(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );
    $merchant->setId('1');
    $userDao->save($merchant);

    $client = $createUser->createUser(
        'foo',
        'bar',
        'client@foo.com',
        Locale::EN(),
        Role::CLIENT()
    );
    $client->setId('2');
    $userDao->save($client);

    $createCompany->createCompany(
        $merchant,
        'bar'
    );
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
        ['foo', null],
        ['foo', null],
    ])
    ->group('company');

it(
    'throws an exception if invalid company',
    function (
        string $merchantId,
        string $name,
        ?string $website
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);

        $createCompany->createCompany(
            $userDao->getById($merchantId),
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
