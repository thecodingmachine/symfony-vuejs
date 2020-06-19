<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\UseCase\User\CreateUser;
use App\UseCase\User\DeleteUser;
use TheCodingMachine\TDBM\TDBMException;

it(
    'deletes the user',
    function (): void {
        $createUser            = self::$container->get(CreateUser::class);
        $deleteUser            = self::$container->get(DeleteUser::class);
        $userDao               = self::$container->get(UserDao::class);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($createUser instanceof CreateUser);
        assert($deleteUser instanceof DeleteUser);
        assert($userDao instanceof UserDao);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        $user = $createUser->createUser(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );

        assertCount(1, $resetPasswordTokenDao->findAll());

        $deleteUser->deleteUser($user);

        assertCount(0, $resetPasswordTokenDao->findAll());
        // TODO test if company link deleted.
        $resetPasswordTokenDao->getById($user->getId());
    }
)
    ->throws(TDBMException::class);
