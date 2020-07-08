<?php

declare(strict_types=1);

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use App\UseCase\User\GetUser;

it(
    'gets a user',
    function (): void {
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $getUser = self::$container->get(GetUser::class);
        assert($getUser instanceof GetUser);

        $user = new User(
            'foo',
            'bar',
            'merchant@foo.com',
            strval(Locale::EN()),
            strval(Role::MERCHANT())
        );
        $userDao->save($user);

        $foundUser = $getUser->user($user);
        assertEquals($user, $foundUser);
    }
)
    ->group('user');
