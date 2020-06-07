<?php

declare(strict_types=1);

use App\Application\User\CreateUser;
use App\Application\User\GetUser;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Throwable\NotFound\UserNotFoundById;

it(
    'gets a user',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        $getUser    = self::$container->get(GetUser::class);
        assert($createUser instanceof CreateUser);
        assert($getUser instanceof GetUser);

        $user = $createUser->create(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );

        $foundUser = $getUser->byId($user->getId());
        assertEquals($user->getId(), $foundUser->getId());
    }
);

it(
    'throws an exception if invalid id.',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        $getUser    = self::$container->get(GetUser::class);
        assert($createUser instanceof CreateUser);
        assert($getUser instanceof GetUser);

        $createUser->create(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );

        $getUser->byId('foo');
    }
)
    ->throws(UserNotFoundById::class);
