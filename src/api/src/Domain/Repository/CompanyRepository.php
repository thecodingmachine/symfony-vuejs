<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Company;
use App\Domain\Model\Filter\CompaniesFilters;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompaniesFilters;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use TheCodingMachine\TDBM\ResultIterator;

interface CompanyRepository
{
    /**
     * @throws InvalidCompany
     */
    public function save(Company $company): void;

    /**
     * @throws CompanyNotFoundById
     */
    public function mustFindOneById(string $id): Company;

    /**
     * @throws CompanyWithNameExists
     */
    public function mustNotFindOneByName(string $name): void;

    /**
     * @return Company[]|ResultIterator
     *
     * @throws InvalidCompaniesFilters
     */
    public function search(CompaniesFilters $filters): ResultIterator;
}
