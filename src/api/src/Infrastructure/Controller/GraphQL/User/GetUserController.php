<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\UserNotFoundById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetUserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws UserNotFoundById
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function user(string $id) : User
    {
        // There is no need to create a dedicated use case
        // for that action.
        return $this->userRepository->mustFindOneById($id);
    }
}
