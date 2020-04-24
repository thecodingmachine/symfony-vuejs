<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User;
use App\Domain\Throwable\Exist\UserWithEmailExist;

interface UserRepository
{
    public function create(User $user) : void;

    public function update(User $user) : void;

    /**
     * @throws UserWithEmailExist
     */
    public function mustNotFindOneWithEmail(string $email) : void;
}
