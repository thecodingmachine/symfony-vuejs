<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Throwable\InvalidModel;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateCompany
{
    private CompanyDao $companyDao;

    public function __construct(CompanyDao $companyDao)
    {
        $this->companyDao = $companyDao;
    }

    /**
     * @throws InvalidModel
     *
     * @Mutation
     */
    public function updateCompany(
        Company $company,
        string $name,
        ?string $website
    ): Company {
        $company->setName($name);
        $company->setWebsite($website);

        $this->companyDao->save($company);

        return $company;
    }
}
