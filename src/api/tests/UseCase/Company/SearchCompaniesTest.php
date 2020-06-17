<?php

declare(strict_types=1);

use App\Domain\Enum\Filter\CompaniesSortByEnum;
use App\Domain\Enum\Filter\SortOrderEnum;
use App\Domain\Model\Company;
use App\Domain\Throwable\Invalid\InvalidCompaniesFilters;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\SearchCompanies;

beforeEach(function (): void {
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);

    $createCompany->create(
        'a',
        'http://a.a'
    );

    $createCompany->create(
        'b',
        'http://b.b'
    );

    $createCompany->create(
        'c',
        'http://c.c'
    );
});

it(
    'finds all companies',
    function (): void {
        $searchCompanies = self::$container->get(SearchCompanies::class);
        assert($searchCompanies instanceof SearchCompanies);

        $result = $searchCompanies->searchCompanies();
        assertCount(3, $result);
    }
);

it(
    'filters companies with a generic search',
    function (string $search): void {
        $searchCompanies = self::$container->get(SearchCompanies::class);
        assert($searchCompanies instanceof SearchCompanies);

        $result = $searchCompanies->searchCompanies($search);
        assertCount(1, $result);

        $company = $result->first();
        assert($company instanceof Company);
        assertStringContainsStringIgnoringCase($search, $company->getName());
        assertStringContainsStringIgnoringCase($search, $company->getWebsite());
    }
)
    ->with(['a', 'b', 'c']);

it(
    'sorts companies by name',
    function (string $sortOrder): void {
        $searchCompanies = self::$container->get(SearchCompanies::class);
        assert($searchCompanies instanceof SearchCompanies);

        $result = $searchCompanies->searchCompanies(null, CompaniesSortByEnum::NAME, $sortOrder);
        assertCount(3, $result);

        /** @var Company[] $companies */
        $companies = $result->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
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
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'sorts companies by website',
    function (string $sortOrder): void {
        $searchCompanies = self::$container->get(SearchCompanies::class);
        assert($searchCompanies instanceof SearchCompanies);

        $result = $searchCompanies->searchCompanies(null, CompaniesSortByEnum::WEBSITE, $sortOrder);
        assertCount(3, $result);

        /** @var Company[] $companies */
        $companies = $result->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
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
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'throws an exception if invalid filters',
    function (string $sortBy, string $sortOrder): void {
        $searchCompanies = self::$container->get(SearchCompanies::class);
        assert($searchCompanies instanceof SearchCompanies);

        $searchCompanies->searchCompanies(null, $sortBy, $sortOrder);
    }
)
    ->with([
        // Invalid sort by.
        ['foo', SortOrderEnum::ASC],
        // Invalid sort order.
        [CompaniesSortByEnum::NAME, 'foo'],
    ])
    ->throws(InvalidCompaniesFilters::class);
