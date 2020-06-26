<?php

declare(strict_types=1);

use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
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
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );

        $foundUser = $getUser->user($user);
        assertEquals($user->getId(), $foundUser->getId());
    }
);
