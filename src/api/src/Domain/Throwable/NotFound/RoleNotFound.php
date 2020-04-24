<?php

declare(strict_types=1);

namespace App\Domain\Throwable\NotFound;

final class RoleNotFound extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct('Role with id ' . $id . ' has not been found');
    }
}
