<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\User\ResetPassword\ResetPassword;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;

final class CreateUser
{
    private UserRepository $userRepository;
    private ResetPassword $resetPassword;

    public function __construct(
        UserRepository $userRepository,
        ResetPassword $resetPassword
    ) {
        $this->userRepository = $userRepository;
        $this->resetPassword  = $resetPassword;
    }

    /**
     * @throws UserWithEmailExist
     * @throws InvalidUser
     * @throws UserNotFoundByEmail
     */
    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ) : User {
        $this->userRepository->mustNotFindOneByEmail($email);

        $user = new User(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $this->userRepository->save($user);
        $this->resetPassword->reset($email);

        return $user;
    }
}
