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
use App\UseCase\Company\DeleteCompanyLogo\DeleteCompanyLogoTask;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;
use Throwable;

final class CreateCompany
{
    private CompanyDao $companyDao;
    private CompanyLogoStorage $companyLogoStorage;
    private MessageBusInterface $messageBus;

    public function __construct(
        CompanyDao $companyDao,
        CompanyLogoStorage $companyLogoStorage,
        MessageBusInterface $messageBus
    ) {
        $this->companyDao         = $companyDao;
        $this->companyLogoStorage = $companyLogoStorage;
        $this->messageBus         = $messageBus;
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

        $fileName = null;
        if ($logo !== null) {
            $fileName = $this->companyLogoStorage->write($logo);
        }

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

        $task = new DeleteCompanyLogoTask($fileName);
        $this->messageBus->dispatch($task);
    }
}
