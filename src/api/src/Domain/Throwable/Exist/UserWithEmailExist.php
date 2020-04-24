<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Exist;

final class UserWithEmailExist extends Exist
{
    public function __construct(string $email)
    {
        parent::__construct($email . ' is already assigned to a user');
    }

    public function isClientSafe() : bool
    {
        return false;
    }
}
