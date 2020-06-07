<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Company;

use App\Application\Company\SearchCompanies;
use App\Domain\Model\Company;
use App\Domain\Throwable\Invalid\InvalidCompaniesFilters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\TDBM\ResultIterator;

final class SearchCompaniesController extends AbstractController
{
    private SearchCompanies $searchCompanies;

    public function __construct(SearchCompanies $searchCompanies)
    {
        $this->searchCompanies = $searchCompanies;
    }

    /**
     * @return Company[]|ResultIterator
     *
     * @throws InvalidCompaniesFilters
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function searchCompanies(
        ?string $search = null,
        ?string $sortBy = null,
        ?string $sortOrder = null
    ): ResultIterator {
        return $this->searchCompanies->search(
            $search,
            $sortBy,
            $sortOrder
        );
    }
}
