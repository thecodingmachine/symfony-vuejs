<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $userEntity = new User();
        $userEntity->setLogin('foo');
        $userEntity->setPlainPassword('bar');
        $userEntity->setRoles(['ROLE_FOO']);
        $manager->persist($userEntity);
        $manager->flush();
    }
}
