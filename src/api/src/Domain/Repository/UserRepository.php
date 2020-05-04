<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\User;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\Domain\Throwable\NotFound\UserNotFoundById;

interface UserRepository
{
    public function save(User $user) : void;

    /**
     * @throws UserNotFoundById
     */
    public function mustFindOneById(string $id) : User;

    /**
     * @throws UserNotFoundByEmail
     */
    public function mustFindOneByEmail(string $email) : User;

    /**
     * @throws UserWithEmailExist
     */
    public function mustNotFindOneByEmail(string $email) : void;
}
