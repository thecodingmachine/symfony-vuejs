<?php

declare(strict_types=1);

namespace App\Domain\Throwable\NotFound;

final class UserNotFoundByEmail extends NotFound
{
    public function __construct(string $email)
    {
        parent::__construct('"' . $email . '" is not assigned to a user');
    }

    public function isClientSafe() : bool
    {
        return false;
    }
}
