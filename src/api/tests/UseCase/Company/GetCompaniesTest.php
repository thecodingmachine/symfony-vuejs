<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Filter\CompaniesSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\UseCase\Company\GetCompanies;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;

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
        'a'
    );
    $company->setWebsite('http://a.a');
    $companyDao->save($company);

    $company = new Company(
        $merchant,
        'b'
    );
    $company->setWebsite('http://b.b');
    $companyDao->save($company);

    $company = new Company(
        $merchant,
        'c'
    );
    $company->setWebsite('http://c.c');
    $companyDao->save($company);
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
        if ($sortOrder->equals(SortOrder::ASC())) {
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
        if ($sortOrder->equals(SortOrder::ASC())) {
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
