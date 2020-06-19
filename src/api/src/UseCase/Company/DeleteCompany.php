<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\Domain\Storage\CompanyLogoStorage;
use App\UseCase\Product\DeleteProduct;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class DeleteCompany
{
    private CompanyDao $companyDao;
    private CompanyLogoStorage $companyLogoStorage;
    private DeleteProduct $deleteProduct;

    public function __construct(
        CompanyDao $companyDao,
        CompanyLogoStorage $companyLogoStorage,
        DeleteProduct $deleteProduct
    ) {
        $this->companyDao         = $companyDao;
        $this->companyLogoStorage = $companyLogoStorage;
        $this->deleteProduct      = $deleteProduct;
    }

    /**
     * @Mutation
     * @Security("is_granted('CAN_DELETE', company)")
     */
    public function deleteCompany(Company $company): bool
    {
        $products = $company->getProducts();
        $logo     = $company->getLogo();

        foreach ($products as $product) {
            $this->deleteProduct->deleteProduct($product);
        }

        $this->companyDao->delete($company);

        if ($logo === null) {
            return true;
        }

        $this->companyLogoStorage->delete($logo);

        return true;
    }
}
