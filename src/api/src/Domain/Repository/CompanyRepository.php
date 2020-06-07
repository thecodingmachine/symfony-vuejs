<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Company;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;

interface CompanyRepository
{
    /**
     * @throws InvalidCompany
     */
    public function save(Company $company): void;

    /**
     * @throws CompanyWithNameExists
     */
    public function mustNotFindOneByName(string $name): void;
}
