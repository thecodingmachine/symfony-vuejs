<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\GetCompany;
use App\Domain\Model\Company;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetCompanyByIdController extends AbstractController
{
    private GetCompany $getCompany;

    public function __construct(GetCompany $getCompany)
    {
        $this->getCompany = $getCompany;
    }

    /**
     * @throws CompanyNotFoundById
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function getCompanyById(string $id): Company
    {
        return $this->getCompany->byId($id);
    }
}
