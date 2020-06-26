<?php

declare(strict_types=1);

namespace App\UseCase\Company\DeleteCompanyLogo;

use App\Domain\Storage\CompanyLogoStorage;

final class DeleteCompanyLogo
{
    private CompanyLogoStorage $companyLogoStorage;

    public function __construct(CompanyLogoStorage $companyLogoStorage)
    {
        $this->companyLogoStorage = $companyLogoStorage;
    }

    public function deleteCompanyLogo(string $logo): void
    {
        $this->companyLogoStorage->delete($logo);
    }
}
