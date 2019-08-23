<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public const DEFAULT_USER_LOGIN = 'foo';

    public const DEFAULT_USER_PASSWORD = 'bar';

    public const USER_LOGIN_ROLE_BAR = 'bar';

    public const USER_PASSWORD_ROLE_BAR = 'foo';

    public function load(ObjectManager $manager): void
    {
        $this->createUser($manager, self::DEFAULT_USER_LOGIN, self::DEFAULT_USER_PASSWORD, ['ROLE_FOO']);
        $this->createUser($manager, self::USER_LOGIN_ROLE_BAR, self::USER_PASSWORD_ROLE_BAR, ['ROLE_BAR']);
    }

    /**
     * @param string[] $roles
     */
    private function createUser(ObjectManager $manager, string $login, string $password, array $roles): void
    {
        $userEntity = new User();
        $userEntity->setLogin($login);
        $userEntity->setPlainPassword($password);
        $userEntity->setRoles($roles);
        $manager->persist($userEntity);
        $manager->flush();
    }
}
