<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Enum\Filter\CompaniesSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Model\Company;
use App\Domain\ResultIterator\CompanyResultIterator;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetCompanies
{
    private CompanyDao $companyDao;

    public function __construct(CompanyDao $companyDao)
    {
        $this->companyDao = $companyDao;
    }

    /**
     * @return Company[]|CompanyResultIterator
     *
     * @Query
     */
    public function companies(
        ?string $search = null,
        ?CompaniesSortBy $sortBy = null,
        ?SortOrder $sortOrder = null
    ): CompanyResultIterator {
        return $this->companyDao->search(
            $search,
            null,
            $sortBy,
            $sortOrder
        );
    }
}
