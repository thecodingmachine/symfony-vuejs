<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Model\Company;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class GetCompany
{
    /**
     * @Query
     * @Logged
     * @Security("is_granted('GET_COMPANY', company)")
     */
    public function company(Company $company): Company
    {
        // GraphQLite black magic.
        return $company;
    }
}
