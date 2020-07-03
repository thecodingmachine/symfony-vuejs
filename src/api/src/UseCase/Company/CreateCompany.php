<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreateCompany
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
     * @InjectUser(for="$merchant")
     */
    public function createCompany(
        User $merchant,
        string $name,
        ?string $website = null
    ): Company {
        $company = new Company($merchant, $name);
        $company->setWebsite($website);

        $this->companyDao->save($company);

        return $company;
    }
}
