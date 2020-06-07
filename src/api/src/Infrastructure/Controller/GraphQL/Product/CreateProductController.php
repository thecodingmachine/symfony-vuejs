<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Product;

use App\Application\Product\CreateProduct;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\Domain\Throwable\NotFound\CompanyNotFoundById;
use App\Infrastructure\Factory\StorableFactory;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateProductController extends AbstractController
{
    private CreateProduct $createProduct;

    public function __construct(CreateProduct $createProduct)
    {
        $this->createProduct = $createProduct;
    }

    /**
     * @param UploadedFileInterface[]|null $pictures
     *
     * @throws ProductWithNameExists
     * @throws CompanyNotFoundById
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     *
     * @Mutation
     * @Right("ROLE_COMPANY")
     */
    public function createProduct(
        string $name,
        float $price,
        string $companyId,
        ?array $pictures = null
    ): Product {
        $storables = null;
        if ($pictures !== null) {
            /** @var ProductPicture[] $storables */
            $storables = StorableFactory::createAllFromUploadedFiles(
                $pictures,
                ProductPicture::class
            );
        }

        return $this->createProduct->create(
            $name,
            $price,
            $companyId,
            $storables
        );
    }
}
