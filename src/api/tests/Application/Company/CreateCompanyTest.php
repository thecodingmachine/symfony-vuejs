<?php

declare(strict_types=1);

use App\Application\Company\CreateCompany;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use App\Infrastructure\Factory\StorableFactory;
use App\Tests\Application\DummyValues;

it(
    'creates a company',
    function (
        string $name,
        ?string $website
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);

        $company = $createCompany->create(
            $name,
            $website
        );

        assertEquals($name, $company->getName());
        assertEquals($website, $company->getWebsite());
        assertNull($company->getLogo());
    }
)
    ->with([
        ['Foo', null],
        ['Foo', 'http://foo.bar'],
        ['Foo', null],
        ['Foo', null],
    ]);

it(
    'stores the logo',
    function (
        string $logo
    ): void {
        $createCompany    = self::$container->get(CreateCompany::class);
        $companyLogoStore = self::$container->get(CompanyLogoStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($companyLogoStore instanceof CompanyLogoStore);

        $storable = StorableFactory::createFromPath(
            dirname(__FILE__) . '/' . $logo,
            CompanyLogo::class
        );
        assert($storable instanceof CompanyLogo);

        $company = $createCompany->create(
            'foo',
            null,
            $storable
        );

        assertNotNull($company->getLogo());
        assertTrue($companyLogoStore->fileExists($company->getLogo()));
    }
)
    ->with(['foo.png', 'foo.jpg']);

it(
    'throws an exception if name is already associated to a company',
    function (
        string $name
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);

        $createCompany->create($name);
        $createCompany->create($name);
    }
)
    ->with(['foo'])
    ->throws(CompanyWithNameExists::class);

it(
    'throws an exception if invalid company logo',
    function (
        string $logo
    ): void {
        $createCompany    = self::$container->get(CreateCompany::class);
        $companyLogoStore = self::$container->get(CompanyLogoStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($companyLogoStore instanceof CompanyLogoStore);

        $storable = StorableFactory::createFromPath(
            dirname(__FILE__) . '/' . $logo,
            CompanyLogo::class
        );
        assert($storable instanceof CompanyLogo);

        $createCompany->create(
            'foo',
            null,
            $storable
        );

        assertFalse($companyLogoStore->fileExists($logo));
    }
)
    ->with(['foo.txt'])
    ->throws(InvalidCompanyLogo::class);

it(
    'throws an exception if invalid company data',
    function (
        string $name,
        ?string $website
    ): void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);

        $createCompany->create(
            $name,
            $website
        );
    }
)
    ->with([
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
    ->throws(InvalidCompany::class);

it(
    'deletes the logo if exception',
    function (
        string $logo
    ): void {
        $createCompany    = self::$container->get(CreateCompany::class);
        $companyLogoStore = self::$container->get(CompanyLogoStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($companyLogoStore instanceof CompanyLogoStore);

        $storable = StorableFactory::createFromPath(
            dirname(__FILE__) . '/' . $logo,
            CompanyLogo::class
        );
        assert($storable instanceof CompanyLogo);

        $createCompany->create(
            DummyValues::BLANK,
            null,
            $storable
        );

        assertFalse($companyLogoStore->fileExists($logo));
    }
)
    ->with(['foo.jpg'])
    ->throws(InvalidCompany::class);
