<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Storage\CompanyLogoStorage;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use Psr\Http\Message\UploadedFileInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;
use Throwable;

final class CreateCompany
{
    private CompanyDao $companyDao;
    private CompanyLogoStorage $companyLogoStorage;

    public function __construct(
        CompanyDao $companyDao,
        CompanyLogoStorage $companyLogoStorage
    ) {
        $this->companyDao         = $companyDao;
        $this->companyLogoStorage = $companyLogoStorage;
    }

    /**
     * @throws CompanyWithNameExists
     * @throws InvalidCompanyLogo
     * @throws InvalidCompany
     *
     * @Mutation
     * @Right("ROLE_COMPANY")
     */
    public function createCompany(
        string $name,
        ?string $website = null,
        ?UploadedFileInterface $logo = null
    ): Company {
        $storable = null;
        if ($logo !== null) {
            $storable = CompanyLogo::createFromUploadedFile($logo);
        }

        return $this->create(
            $name,
            $website,
            $storable
        );
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
        $this->companyDao->mustNotFindOneByName($name);

        $fileName = $logo !== null ?
            $this->companyLogoStorage->write($logo) :
            null;

        $company = new Company($name);
        $company->setWebsite($website);
        $company->setLogo($fileName);

        try {
            $this->companyDao->save($company);
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

        $this->companyLogoStorage->delete($fileName);
    }
}
