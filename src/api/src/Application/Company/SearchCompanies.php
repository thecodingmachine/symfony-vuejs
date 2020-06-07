<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Model\Company;
use App\Domain\Model\Filter\CompaniesFilters;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Throwable\Invalid\InvalidCompaniesFilters;
use TheCodingMachine\TDBM\ResultIterator;

final class SearchCompanies
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @return Company[]|ResultIterator
     *
     * @throws InvalidCompaniesFilters
     */
    public function search(
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ): ResultIterator {
        $filters = new CompaniesFilters($search, $sortBy, $sortOrder);

        return $this->companyRepository->search($filters);
    }
}
