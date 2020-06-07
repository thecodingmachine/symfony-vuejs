<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;

final class GetCompany
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws CompanyNotFoundById
     */
    public function byId(string $id): Company
    {
        return $this->companyRepository->mustFindOneById($id);
    }
}
