<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser;

use App\Application\User\ResetPassword\ResetPassword;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateUser
{
    private ValidatorInterface $validator;
    private UserRepository $userRepository;
    private ResetPassword $resetPassword;

    public function __construct(
        ValidatorInterface $validator,
        UserRepository $userRepository,
        ResetPassword $resetPassword
    ) {
        $this->validator      = $validator;
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
        string $role
    ) : User {
        $this->userRepository->mustNotFindOneByEmail($email);

        $user = new User(
            $firstName,
            $lastName,
            $email,
            $role
        );

        $violations = $this->validator->validate($user);
        InvalidUser::throwException($violations);

        $this->userRepository->save($user);
        $this->resetPassword->reset($email);

        return $user;
    }
}
