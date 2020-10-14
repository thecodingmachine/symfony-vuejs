<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;
use function in_array;

final class CompanyVoter extends AppVoter
{
    public const CREATE_PRODUCT = 'CREATE_PRODUCT';
    public const UPDATE_COMPANY = 'UPDATE_COMPANY';
    public const GET_COMPANY    = 'GET_COMPANY';

    /**
     * @param mixed $subject
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (
            ! in_array(
                $attribute,
                [
                    self::CREATE_PRODUCT,
                    self::UPDATE_COMPANY,
                    self::GET_COMPANY,
                ]
            )
        ) {
            return false;
        }

        return $subject instanceof Company;
    }

    /**
     * @param mixed $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // If the user is anonymous, do not grant access
        if (! $user instanceof UserInterface) {
            return false;
        }

        assert($user instanceof User);
        assert($subject instanceof Company);

        // Remember: thanks to role hierarchy, an administrator has
        // all roles.
        if (! $this->security->isGranted(Role::getSymfonyRole(Role::MERCHANT()))) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_PRODUCT:
            case self::UPDATE_COMPANY:
            case self::GET_COMPANY:
                // User should own the company.
                return $subject->getUser()->getId() === $user->getId();

            default:
                return false;
        }
    }
}
