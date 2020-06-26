<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetUser
{
    /**
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function user(User $user): User
    {
        return $user;
    }
}
