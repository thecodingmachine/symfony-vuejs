<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetUser
{
    /**
     * @Query
     */
    public function user(User $user): User
    {
        // GraphQLite black magic.
        return $user;
    }
}
