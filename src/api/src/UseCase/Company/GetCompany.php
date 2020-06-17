<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetCompany
{
    private CompanyDao $companyDao;

    public function __construct(CompanyDao $companyDao)
    {
        $this->companyDao = $companyDao;
    }

    /**
     * @throws CompanyNotFoundById
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function getCompanyById(string $id): Company
    {
        return $this->companyDao->mustFindOneById($id);
    }
}
