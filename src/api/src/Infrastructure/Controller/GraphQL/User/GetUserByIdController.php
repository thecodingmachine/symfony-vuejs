<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetUser;
use App\Domain\Model\User;
use App\Domain\Throwable\NotFound\UserNotFoundById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetUserByIdController extends AbstractController
{
    private GetUser $getUser;

    public function __construct(GetUser $getUser)
    {
        $this->getUser = $getUser;
    }

    /**
     * @throws UserNotFoundById
     *
     * @Query
     * @Right("ROLE_ADMINISTRATOR")
     */
    public function getUserById(string $id): User
    {
        return $this->getUser->byId($id);
    }
}
