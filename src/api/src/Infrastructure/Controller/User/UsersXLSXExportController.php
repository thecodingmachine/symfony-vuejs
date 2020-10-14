<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\User;

use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Filter\UsersSortBy;
use App\Domain\Enum\Role;
use App\Infrastructure\Controller\DownloadXLSXController;
use App\UseCase\User\CreateUsersXLSXExport;
use App\UseCase\User\GetUsers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;
use function is_string;

final class UsersXLSXExportController extends DownloadXLSXController
{
    private GetUsers $getUsers;
    private CreateUsersXLSXExport $createUsersXLSXExport;

    public function __construct(GetUsers $getUsers, CreateUsersXLSXExport $createUsersXLSXExport)
    {
        $this->getUsers              = $getUsers;
        $this->createUsersXLSXExport = $createUsersXLSXExport;
    }

    /**
     * @Route("/users/xlsx", methods={"GET"})
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     */
    public function downloadUsersXlsx(Request $request): Response
    {
        $locale = $request->query->get('locale', $request->getLocale());
        assert(is_string($locale));
        $search    = $request->query->get('search', null);
        $role      = $request->query->get('role', null);
        $sortBy    = $request->query->get('sortBy', null);
        $sortOrder = $request->query->get('sortOrder', null);

        $users = $this->getUsers->users(
            $search,
            $role ? Role::$role() : null,
            $sortBy ? UsersSortBy::$sortBy() : null,
            $sortOrder ? SortOrder::$sortOrder() : null
        );

        $xlsx = $this->createUsersXLSXExport->createXLSX(
            $locale,
            $users
        );

        return $this->createResponseWithXLSXAttachment(
            'users.xlsx',
            $xlsx
        );
    }
}
