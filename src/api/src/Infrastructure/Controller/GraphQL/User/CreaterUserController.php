<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\CreateUser\CreateUser;
use App\Application\User\CreateUser\InvalidUser;
use App\Domain\Model\User;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreaterUserController extends AbstractController
{
    private CreateUser $createUser;

    public function __construct(CreateUser $createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @throws UserWithEmailExist
     * @throws UserNotFoundByEmail
     * @throws InvalidUser
     *
     * @Mutation
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $role
    ) : User {
        return $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $role
        );
    }
}
