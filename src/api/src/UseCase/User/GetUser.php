<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class GetUser
{
    /**
     * @Query
     * @Security("is_granted('GET_USER', user)")
     */
    public function user(User $user): User
    {
        // GraphQLite black magic.
        return $user;
    }
}
