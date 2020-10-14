<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use App\Domain\Enum\Role;
use App\Domain\Model\Product;
use App\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;
use function in_array;

final class ProductVoter extends AppVoter
{
    public const UPDATE_PRODUCT = 'UPDATE_PRODUCT';
    public const DELETE_PRODUCT = 'DELETE_PRODUCT';
    public const CREATE_ORDER   = 'CREATE_ORDER';

    /**
     * @param mixed $subject
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (
            ! in_array(
                $attribute,
                [
                    self::UPDATE_PRODUCT,
                    self::DELETE_PRODUCT,
                    self::CREATE_ORDER,
                ]
            )
        ) {
            return false;
        }

        return $subject instanceof Product;
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
        assert($subject instanceof Product);

        switch ($attribute) {
            case self::UPDATE_PRODUCT:
            case self::DELETE_PRODUCT:
                // Remember: thanks to role hierarchy, an administrator has
                // all roles.
                if (! $this->security->isGranted(Role::getSymfonyRole(Role::MERCHANT()))) {
                    return false;
                }

                // User should own the product's company.
                return $subject->getCompany()->getUser()->getId() === $user->getId();

            case self::CREATE_ORDER:
                // User should not own the product's company.
                return $subject->getCompany()->getUser()->getId() !== $user->getId();

            default:
                return false;
        }
    }
}
