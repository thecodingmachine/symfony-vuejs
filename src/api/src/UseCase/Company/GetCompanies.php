<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Model\Filter\CompaniesFilters;
use App\Domain\Throwable\Invalid\InvalidCompaniesFilters;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetCompanies
{
    private CompanyDao $companyDao;

    public function __construct(CompanyDao $companyDao)
    {
        $this->companyDao = $companyDao;
    }

    /**
     * @return Company[]|ResultIterator
     *
     * @throws InvalidCompaniesFilters
     *
     * @Query
     */
    public function companies(
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ): ResultIterator {
        $filters = new CompaniesFilters($search, $sortBy, $sortOrder);

        return $this->companyDao->search($filters);
    }
}
