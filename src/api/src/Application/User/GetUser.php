<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\UserNotFoundById;

final class GetUser
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws UserNotFoundById
     */
    public function byId(string $id): User
    {
        return $this->userRepository->mustFindOneById($id);
    }
}
