<?php

declare(strict_types=1);

use App\Application\User\CreateUser;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Throwable\Exists\UserWithEmailExists;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Tests\Application\DummyValues;

it(
    'creates a user',
    function (
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);

        $user = $createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        assertEquals($firstName, $user->getFirstName());
        assertEquals($lastName, $user->getLastName());
        assertEquals($email, $user->getEmail());
        assertNull($user->getPassword());
        assertEquals($locale, $user->getLocale());
        assertEquals($role, $user->getRole());
    }
)
    ->with([
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::EN, RoleEnum::COMPANY],
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::FR, RoleEnum::CLIENT],
    ]);

it(
    'throws an exception if an e-mail is already associated to a user',
    function (
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);

        $createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }
)
    ->with([
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
    ])
    ->throws(UserWithEmailExists::class);

it(
    'throws an exception if invalid user data',
    function (
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);

        $createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }
)
    ->with([
        // Blank first name.
        [DummyValues::BLANK, 'Bar', 'foo', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        // First name > 255.
        [DummyValues::CHAR256, 'Bar', 'foo', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        // Blank last name.
        ['Foo', DummyValues::BLANK, 'foo', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        // Last name > 255.
        ['Foo', DummyValues::CHAR256, 'foo', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        // Invalid e-mail.
        ['Foo', 'Bar', 'foo', LocaleEnum::EN, RoleEnum::ADMINISTRATOR],
        // Invalid locale.
        ['Foo', 'Bar', 'foo.bar@baz.com', 'foo', RoleEnum::ADMINISTRATOR],
        // Invalid role.
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::EN, 'foo'],
    ])
    ->throws(InvalidUser::class);
