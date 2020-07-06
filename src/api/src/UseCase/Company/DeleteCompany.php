<?php

declare(strict_types=1);

namespace App\UseCase\Company;

use App\Domain\Dao\CompanyDao;
use App\Domain\Model\Company;
use App\UseCase\Product\DeleteProduct;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

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
