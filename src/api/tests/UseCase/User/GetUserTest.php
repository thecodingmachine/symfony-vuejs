<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\User\CreateUser;
use App\UseCase\User\GetUser;

it(
    'gets a user',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        $getUser    = self::$container->get(GetUser::class);
        assert($createUser instanceof CreateUser);
        assert($getUser instanceof GetUser);

        $user = $createUser->createUser(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            Locale::EN,
            Role::ADMINISTRATOR
        );

        $foundUser = $getUser->user($user);
        assertEquals($user->getId(), $foundUser->getId());
    }
);
