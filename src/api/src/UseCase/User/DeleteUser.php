<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Dao\UserDao;
use App\Domain\Model\User;
use App\UseCase\Company\DeleteCompany;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

final class DeleteUser
{
    private DeleteCompany $deleteCompany;
    private UserDao $userDao;

    public function __construct(
        DeleteCompany $deleteCompany,
        UserDao $userDao
    ) {
        $this->deleteCompany = $deleteCompany;
        $this->userDao       = $userDao;
    }

    /**
     * @Mutation
     * @Logged
     * @Security("is_granted('ROLE_ADMINISTRATOR')")
     */
    public function deleteUser(User $user): bool
    {
        // If the user is a merchant, we have to
        // delete his companies.
        foreach ($user->getCompanies() as $company) {
            $this->deleteCompany->deleteCompany($company);
        }

        // Cascade = true will also delete the reset
        // password token (if any).
        $this->userDao->delete($user, true);

        return true;
    }
}
