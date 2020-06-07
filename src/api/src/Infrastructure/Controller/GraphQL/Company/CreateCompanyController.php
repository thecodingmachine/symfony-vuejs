<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\CreateCompany;
use App\Domain\Model\Company;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use App\Infrastructure\Factory\StorableFactory;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

use function assert;

final class CreateCompanyController extends AbstractController
{
    private CreateCompany $createCompany;

    public function __construct(CreateCompany $createCompany)
    {
        $this->createCompany = $createCompany;
    }

    /**
     * @throws CompanyWithNameExists
     * @throws InvalidCompanyLogo
     * @throws InvalidCompany
     *
     * @Mutation

     */
    public function createCompany(
        string $name,
        ?string $website = null,
        ?UploadedFileInterface $logo = null
    ): Company {
        $storable = null;
        if ($logo !== null) {
            $storable = StorableFactory::createFromUploadedFile(
                $logo,
                CompanyLogo::class
            );
            assert($storable instanceof CompanyLogo);
        }

        return $this->createCompany->create(
            $name,
            $website,
            $storable
        );
    }
}
