<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\UseCase\Company\DeleteCompanyLogo\DeleteCompanyLogoTask;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class DeleteCompany
{
    private CompanyDao $companyDao;
    private MessageBusInterface $messageBus;

    public function __construct(CompanyDao $companyDao, MessageBusInterface $messageBus)
    {
        $this->companyDao = $companyDao;
        $this->messageBus = $messageBus;
    }

    /**
     * @Mutation
     * @Security("is_granted('NA', company)")
     */
    public function deleteCompany(Company $company): bool
    {
        $logo     = $company->getLogo();
        $pictures = $company->getProductsPictures();

        $this->companyDao->delete($company, true);

        if ($logo !== null) {
            $task = new DeleteCompanyLogoTask($logo);
            $this->messageBus->dispatch($task);
        }

        if (! empty($pictures)) {
            $task = new DeleteProductPicturesTask($pictures);
            $this->messageBus->dispatch($task);
        }

        return true;
    }
}
