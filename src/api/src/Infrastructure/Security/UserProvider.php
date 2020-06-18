<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Dao\UserDao;
use App\Domain\Model\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function get_class;
use function is_a;
use function Safe\sprintf;

final class UserProvider implements UserProviderInterface
{
    private UserDao $userDao;

    public function __construct(UserDao $userDao)
    {
        $this->userDao = $userDao;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->userDao->findOneByEmail($username);
        if ($user !== null) {
            return $user;
        }

        throw new UsernameNotFoundException(
            'No user found for email ' . $username
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return is_a($class, User::class, true);
    }
}
