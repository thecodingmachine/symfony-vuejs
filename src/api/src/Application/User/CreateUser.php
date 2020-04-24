<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\RoleNotFound;

final class CreateUser
{
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;

    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws RoleNotFound
     * @throws UserWithEmailExist
     */
    public function create(
        string $roleId,
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ) : User {
        $role = $this->roleRepository->mustFindOneById($roleId);
        $this->userRepository->mustNotFindOneWithEmail($email);

        $user = new User(
            $role,
            $firstName,
            $lastName,
            $email
        );
        $user->setPassword($password);
        $this->userRepository->create($user);

        return $user;
    }
}
