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

final class UpdateCompany
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
    public function updateCompany(
        Company $company,
        string $name,
        ?string $website,
        ?UploadedFileInterface $newLogo = null
    ): Company {
        $storable = null;
        if ($newLogo !== null) {
            $storable = CompanyLogo::createFromUploadedFile($newLogo);
        }

        return $this->update(
            $company,
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
    public function update(
        Company $company,
        string $name,
        ?string $website,
        ?CompanyLogo $newLogo = null
    ): Company {
        $this->companyDao->mustNotFindOneByName($name, $company->getId());

        $company->setName($name);
        $company->setWebsite($website);

        $oldFilename = $company->getLogo();
        $newFilename = null;

        if ($newLogo !== null) {
            $newFilename = $this->companyLogoStorage->write($newLogo);
            $company->setLogo($newFilename);
        }

        try {
            $this->companyDao->save($company);
        } catch (InvalidCompany $e) {
            // pepakriz/phpstan-exception-rules limitation: "Catch statement does not know about runtime subtypes".
            // See https://github.com/pepakriz/phpstan-exception-rules#catch-statement-does-not-know-about-runtime-subtypes.
            $this->beforeThrowDeleteNewLogoIfExists($newFilename);

            throw $e;
        } catch (Throwable $e) {
            // If any exception occurs, delete
            // the new logo from the storage.
            $this->beforeThrowDeleteNewLogoIfExists($newFilename);

            throw $e;
        }

        // If a new logo has been provided and
        // there is an old logo, delete the later.
        if ($newFilename !== null && $oldFilename !== null) {
            $task = new DeleteCompanyLogoTask($oldFilename);
            $this->messageBus->dispatch($task);
        }

        return $company;
    }

    private function beforeThrowDeleteNewLogoIfExists(?string $newFilename): void
    {
        if ($newFilename === null) {
            return;
        }

        $task = new DeleteCompanyLogoTask($newFilename);
        $this->messageBus->dispatch($task);
    }
}
