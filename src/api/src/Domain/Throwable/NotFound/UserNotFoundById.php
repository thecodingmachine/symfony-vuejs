<?php

declare(strict_types=1);

namespace App\Domain\Throwable\NotFound;

final class UserNotFoundById extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct('"' . $id . '" identifier is not assigned to a user');
    }

    public function isClientSafe(): bool
    {
        return false;
    }
}
