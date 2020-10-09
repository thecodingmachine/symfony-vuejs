<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\User;

use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Filter\UsersSortBy;
use App\Domain\Enum\Role;
use App\Infrastructure\Controller\DownloadXlsxController;
use App\UseCase\User\CreateUsersXlsx;
use App\UseCase\User\GetUsers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function assert;
use function is_string;

final class UsersXlsxController extends DownloadXlsxController
{
    private GetUsers $getUsers;
    private CreateUsersXlsx $createUsersXlsx;

    public function __construct(GetUsers $getUsers, CreateUsersXlsx $createUsersXlsx)
    {
        $this->getUsers        = $getUsers;
        $this->createUsersXlsx = $createUsersXlsx;
    }

    /**
     * @Route("/users/xlsx", methods={"GET"})
     */
    public function downloadUsersXlsx(Request $request): Response
    {
        // check if authenticated user is admin.

        // TODO enum and locale helper?

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

        $xlsx = $this->createUsersXlsx->createXlsx(
            $locale,
            $users
        );

        return $this->createResponseWithXlsxAttachment(
            'users.xlsx',
            $xlsx
        );
    }
}
