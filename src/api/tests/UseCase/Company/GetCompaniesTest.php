<?php

declare(strict_types=1);

use App\Domain\Enum\Filter\CompaniesSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\GetCompanies;
use App\UseCase\User\CreateUser;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);

    $merchant = $createUser->createUser(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );

    $createCompany->createCompany(
        $merchant,
        'a',
        'http://a.a'
    );

    $createCompany->createCompany(
        $merchant,
        'b',
        'http://b.b'
    );

    $createCompany->createCompany(
        $merchant,
        'c',
        'http://c.c'
    );
});

it(
    'finds all companies',
    function (): void {
        $searchCompanies = self::$container->get(GetCompanies::class);
        assert($searchCompanies instanceof GetCompanies);

        $result = $searchCompanies->companies();
        assertCount(3, $result);
    }
)
    ->group('company');

it(
    'filters companies with a generic search',
    function (string $search): void {
        $searchCompanies = self::$container->get(GetCompanies::class);
        assert($searchCompanies instanceof GetCompanies);

        $result = $searchCompanies->companies($search);
        assertCount(1, $result);

        $company = $result->first();
        assert($company instanceof Company);
        assertStringContainsStringIgnoringCase($search, $company->getName());
        assertStringContainsStringIgnoringCase($search, $company->getWebsite());
    }
)
    ->with(['a', 'b', 'c'])
    ->group('company');

it(
    'sorts companies by name',
    function (SortOrder $sortOrder): void {
        $searchCompanies = self::$container->get(GetCompanies::class);
        assert($searchCompanies instanceof GetCompanies);

        $result = $searchCompanies->companies(null, CompaniesSortBy::NAME(), $sortOrder);
        assertCount(3, $result);

        /** @var Company[] $companies */
        $companies = $result->toArray();
        if ($sortOrder === SortOrder::ASC()) {
            assertStringContainsStringIgnoringCase('a', $companies[0]->getName());
            assertStringContainsStringIgnoringCase('b', $companies[1]->getName());
            assertStringContainsStringIgnoringCase('c', $companies[2]->getName());
        } else {
            assertStringContainsStringIgnoringCase('a', $companies[2]->getName());
            assertStringContainsStringIgnoringCase('b', $companies[1]->getName());
            assertStringContainsStringIgnoringCase('c', $companies[0]->getName());
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('company');

it(
    'sorts companies by website',
    function (SortOrder $sortOrder): void {
        $searchCompanies = self::$container->get(GetCompanies::class);
        assert($searchCompanies instanceof GetCompanies);

        $result = $searchCompanies->companies(null, CompaniesSortBy::WEBSITE(), $sortOrder);
        assertCount(3, $result);

        /** @var Company[] $companies */
        $companies = $result->toArray();
        if ($sortOrder === SortOrder::ASC()) {
            assertStringContainsStringIgnoringCase('a', $companies[0]->getWebsite());
            assertStringContainsStringIgnoringCase('b', $companies[1]->getWebsite());
            assertStringContainsStringIgnoringCase('c', $companies[2]->getWebsite());
        } else {
            assertStringContainsStringIgnoringCase('a', $companies[2]->getWebsite());
            assertStringContainsStringIgnoringCase('b', $companies[1]->getWebsite());
            assertStringContainsStringIgnoringCase('c', $companies[0]->getWebsite());
        }
    }
)
    ->with([SortOrder::ASC(), SortOrder::DESC()])
    ->group('company');
