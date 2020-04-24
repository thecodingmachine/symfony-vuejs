<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\CreateUser;
use App\Domain\Model\User;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\RoleNotFound;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreaterUserController extends AbstractController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @throws RoleNotFound
     * @throws UserWithEmailExist
     *
     * TODO: security
     *
     * @Mutation
     */
    public function createUser(
        string $roleId,
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ) : User {
        return $this->createUser->create(
            $roleId,
            $firstName,
            $lastName,
            $email,
            $password
        );
    }
}
