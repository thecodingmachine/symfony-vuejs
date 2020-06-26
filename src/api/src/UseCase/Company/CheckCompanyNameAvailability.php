<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CheckCompanyNameAvailability
{
    private CompanyDao $companyDao;

    public function __construct(CompanyDao $companyDao)
    {
        $this->companyDao = $companyDao;
    }

    /**
     * @Query
     * @Right("ROLE_COMPANY")
     */
    public function checkCompanyNameAvailability(string $name): bool
    {
        try {
            $this->companyDao->mustNotFindOneByName($name);

            return true;
        } catch (CompanyWithNameExists $e) {
            return false;
        }
    }
}
