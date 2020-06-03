<?php

declare(strict_types=1);

namespace App\Domain\Throwable\NotFound;

final class ResetPasswordTokenNotFoundById extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct('"' . $id . '" identifier is not assigned to a reset password token');
    }

    public function isClientSafe() : bool
    {
        return false;
    }
}
