<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Role;
use App\Domain\Throwable\NotFound\RoleNotFound;

interface RoleRepository
{
    /**
     * @throws RoleNotFound
     */
    public function mustFindOneById(string $id) : Role;
}
