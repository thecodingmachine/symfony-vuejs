<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Throwable\Exists\UserWithEmailExists;
use App\Domain\Throwable\Invalid\InvalidUser;
use App\Tests\UseCase\DummyValues;
use App\UseCase\User\CreateUser;

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

        $user = $createUser->createUser(
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
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::EN, Role::ADMINISTRATOR],
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::EN, Role::COMPANY],
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::FR, Role::CLIENT],
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

        $createUser->createUser(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $createUser->createUser(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }
)
    ->with([
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::EN, Role::ADMINISTRATOR],
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

        $createUser->createUser(
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
        [DummyValues::BLANK, 'Bar', 'foo', Locale::EN, Role::ADMINISTRATOR],
        // First name > 255.
        [DummyValues::CHAR256, 'Bar', 'foo', Locale::EN, Role::ADMINISTRATOR],
        // Blank last name.
        ['Foo', DummyValues::BLANK, 'foo', Locale::EN, Role::ADMINISTRATOR],
        // Last name > 255.
        ['Foo', DummyValues::CHAR256, 'foo', Locale::EN, Role::ADMINISTRATOR],
        // Invalid e-mail.
        ['Foo', 'Bar', 'foo', Locale::EN, Role::ADMINISTRATOR],
        // Invalid locale.
        ['Foo', 'Bar', 'foo.bar@baz.com', 'foo', Role::ADMINISTRATOR],
        // Invalid role.
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::EN, 'foo'],
    ])
    ->throws(InvalidUser::class);
