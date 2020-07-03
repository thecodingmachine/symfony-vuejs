<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use Symfony\Component\Messenger\MessageBusInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

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
     */
    public function deleteCompany(Company $company): bool
    {
        // As cascade deletion also delete the company
        // products, we have to delete their pictures (if any).
        $pictures = $company->getProductsPictures();
        $this->companyDao->delete($company, true);

        if (empty($pictures)) {
            return true;
        }

        // As the deletion of all the pictures might
        // take some time, we do it asynchronously.
        $task = new DeleteProductsPicturesTask($pictures);
        $this->messageBus->dispatch($task);

        return true;
    }
}
