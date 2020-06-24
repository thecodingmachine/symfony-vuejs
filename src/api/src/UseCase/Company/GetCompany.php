<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Model\Company;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetCompany
{
    /**
     * @Query
     */
    public function company(Company $company): Company
    {
        return $company;
    }
}
