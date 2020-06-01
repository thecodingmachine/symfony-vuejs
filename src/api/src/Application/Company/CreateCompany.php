<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Model\Company;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;

final class CreateCompany
{
    private CompanyLogoStore $companyLogoStore;

    /**
     * @throws InvalidCompanyLogo
     */
    public function create(
        string $name,
        ?string $website = null,
        ?CompanyLogo $logo = null
    ) : Company {
        // TODO must not find one by name.

        $fileName = $logo !== null ? $this->companyLogoStore->put($logo) : null;

        $company = new Company($name, $website); // TODO remove website.
        $company->setWebsite($website);
        $company->setLogoFilename($fileName);

        // TODO save

        return $company;
    }
}
