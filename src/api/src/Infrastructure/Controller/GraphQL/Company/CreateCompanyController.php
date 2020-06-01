<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\CreateCompany;
use App\Domain\Model\Company;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use App\Infrastructure\Factory\StorableFactory;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateCompanyController extends AbstractController
{
    private CreateCompany $createCompany;

    /**
     * @throws InvalidCompanyLogo
     *
     * @Mutation
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function createCompany(
        string $name,
        ?string $website = null,
        ?UploadedFileInterface $uploadedLogo = null
    ) : Company {
        $logo = StorableFactory::createCompanyLogoFromUploadedFileInterface(
            $uploadedLogo
        );

        return $this->createCompany->create(
            $name,
            $website,
            $logo
        );
    }
}
