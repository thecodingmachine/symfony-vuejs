<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\UseCase\Product\DeleteProduct;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class DeleteCompany
{
    private DeleteProduct $deleteProduct;
    private CompanyDao $companyDao;

    public function __construct(
        DeleteProduct $deleteProduct,
        CompanyDao $companyDao
    ) {
        $this->deleteProduct = $deleteProduct;
        $this->companyDao    = $companyDao;
    }

    /**
     * @Mutation
     * @Logged
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     */
    public function deleteCompany(Company $company): bool
    {
        foreach ($company->getProducts() as $product) {
            $this->deleteProduct->deleteProduct($product);
        }

        $this->companyDao->delete($company);

        return true;
    }
}
