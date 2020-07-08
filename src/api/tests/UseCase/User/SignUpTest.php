<?php

declare(strict_types=1);

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use App\UseCase\User\SignUp\SignUp;
use App\UseCase\User\SignUp\WrongRole;

it(
    'signs up a user',
    function (
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ): void {
        $signUp = self::$container->get(SignUp::class);
        assert($signUp instanceof SignUp);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);

        $signUp->signUp(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $user = $userDao->findOneByEmail($email);
        assert($user instanceof User);

        assertEquals($firstName, $user->getFirstName());
        assertEquals($lastName, $user->getLastName());
        assertEquals($email, $user->getEmail());
        assertNull($user->getPassword());
        assertEquals($locale, $user->getLocale());
        assertEquals($role, $user->getRole());
    }
)
    ->with([
        ['foo', 'bar', 'foo.bar@baz.com', Locale::EN(), Role::MERCHANT()],
        ['foo', 'bar', 'foo.bar@baz.com', Locale::FR(), Role::CLIENT()],
    ])
    ->group('user');

it(
    'throws an exception if invalid role',
    function (
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ): void {
        $signUp = self::$container->get(SignUp::class);
        assert($signUp instanceof SignUp);

        $signUp->signUp(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }
)
    ->with([
        ['foo', 'bar', 'foo.bar@baz.com', Locale::EN(), Role::ADMINISTRATOR()],
    ])
    ->throws(WrongRole::class)
    ->group('user');
