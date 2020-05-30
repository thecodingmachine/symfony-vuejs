<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Proxy\PasswordProxy;
use App\Domain\Model\User;
use App\Domain\Repository\Filter\User\InvalidUsersFilters;
use App\Domain\Repository\Filter\User\UsersFilters;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\Domain\Throwable\NotFound\UserNotFoundById;
use TheCodingMachine\TDBM\ResultIterator;

interface UserRepository
{
    /**
     * @throws InvalidUser
     */
    public function save(User $user) : void;

    /**
     * @throws InvalidPassword
     * @throws InvalidUser
     */
    public function updatePassword(User $user, PasswordProxy $passwordProxy) : void;

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

    /**
     * @return User[]|ResultIterator
     *
     * @throws InvalidUsersFilters
     */
    public function search(UsersFilters $filters) : ResultIterator;
}
