<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Model\Company;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use Throwable;

final class CreateCompany
{
    private CompanyRepository $companyRepository;
    private CompanyLogoStore $companyLogoStore;

    public function __construct(
        CompanyRepository $companyRepository,
        CompanyLogoStore $companyLogoStore
    ) {
        $this->companyRepository = $companyRepository;
        $this->companyLogoStore  = $companyLogoStore;
    }

    /**
     * @throws CompanyWithNameExists
     * @throws InvalidCompanyLogo
     * @throws InvalidCompany
     */
    public function create(
        string $name,
        ?string $website = null,
        ?CompanyLogo $logo = null
    ): Company {
        $this->companyRepository->mustNotFindOneByName($name);

        $fileName = $logo !== null ?
            $this->companyLogoStore->write($logo) :
            null;

        $company = new Company($name);
        $company->setWebsite($website);
        $company->setLogo($fileName);

        try {
            $this->companyRepository->save($company);
        } catch (InvalidCompany $e) {
            // pepakriz/phpstan-exception-rules limitation: "Catch statement does not know about runtime subtypes".
            // See https://github.com/pepakriz/phpstan-exception-rules#catch-statement-does-not-know-about-runtime-subtypes.
            $this->beforeThrowDeleteLogoIfExists($fileName);

            throw $e;
        } catch (Throwable $e) {
            // If any exception occurs, delete
            // the logo from the store.
            $this->beforeThrowDeleteLogoIfExists($fileName);

            throw $e;
        }

        return $company;
    }

    private function beforeThrowDeleteLogoIfExists(?string $fileName): void
    {
        if ($fileName === null) {
            return;
        }

        $this->companyLogoStore->delete($fileName);
    }
}
