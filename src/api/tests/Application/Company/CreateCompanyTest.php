<?php

declare(strict_types=1);

use App\Application\Company\CreateCompany;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Infrastructure\Factory\StorableFactory;

it(
    'creates a company',
    function (
        string $name,
        ?string $website
    ) : void {
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);

        $company = $createCompany->create(
            $name,
            $website,
            null
        );

        assertEquals($name, $company->getName());
        assertEquals($website, $company->getWebsite());
        assertNull($company->getLogoFilename());
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
    ) : void {
        $createCompany    = self::$container->get(CreateCompany::class);
        $companyLogoStore = self::$container->get(CompanyLogoStore::class);
        assert($createCompany instanceof CreateCompany);
        assert($companyLogoStore instanceof CompanyLogoStore);

        $logo = StorableFactory::createFromPath(
            dirname(__FILE__) . '/' . $logo,
            CompanyLogo::class
        );
        assert($logo instanceof CompanyLogo);

        $company = $createCompany->create(
            'foo',
            null,
            $logo
        );

        assertNotNull($company->getLogoFilename());
        assertTrue($companyLogoStore->exist($company->getLogoFilename()));
    }
)
    ->with(['foo.png', 'foo.jpg']);
