<?php

declare(strict_types=1);

use App\Application\User\GetUser;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\UserNotFoundById;

beforeEach(function (): void {
});

it(
    'gets a user',
    function (): void {
        $userRepository = self::$container->get(UserRepository::class);
        $getUser        = self::$container->get(GetUser::class);
        assert($userRepository instanceof UserRepository);
        assert($getUser instanceof GetUser);

        $user = new User(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );
        $userRepository->save($user);

        $foundUser = $getUser->byId($user->getId());
        assertEquals($user->getId(), $foundUser->getId());
    }
);

it(
    'throws an exception if invalid id.',
    function (): void {
        $userRepository = self::$container->get(UserRepository::class);
        $getUser        = self::$container->get(GetUser::class);
        assert($userRepository instanceof UserRepository);
        assert($getUser instanceof GetUser);

        $user = new User(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );
        $userRepository->save($user);

        $getUser->byId('foo');
    }
)
    ->throws(UserNotFoundById::class);
