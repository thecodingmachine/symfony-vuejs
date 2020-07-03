<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Tests\UseCase\DummyValues;
use App\UseCase\User\CreateUser;
use App\UseCase\User\ResetPassword\ResetPassword;
use App\UseCase\User\UpdatePassword\ResetPasswordTokenExpired;
use App\UseCase\User\UpdatePassword\UpdatePassword;
use App\UseCase\User\UpdatePassword\WrongResetPasswordToken;
use Safe\DateTimeImmutable;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);

    $createUser->createUser(
        'Foo',
        'Bar',
        'foo.bar@baz.com',
        Locale::EN,
        Role::ADMINISTRATOR
    );
});

it(
    'updates the password and deletes the token',
    function (string $email, string $password): void {
        $resetPassword         = self::$container->get(ResetPassword::class);
        $updatePassword        = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        $notification       = $resetPassword->reset($email);
        $resetPasswordToken = $resetPasswordTokenDao->getById($notification->getResetPasswordTokenId());

        $user = $updatePassword->update(
            $resetPasswordToken,
            $notification->getPlainToken(),
            $password
        );

        assertTrue(password_verify($password, $user->getPassword()));
        $resetPasswordTokenDao->getById($notification->getResetPasswordTokenId());
    }
)
    ->with([['foo.bar@baz.com', 'foobarfoo']])
    ->throws(TDBMException::class);

it(
    'throws an exception if wrong token',
    function (string $email, string $password): void {
        $resetPassword         = self::$container->get(ResetPassword::class);
        $updatePassword        = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        $notification       = $resetPassword->reset($email);
        $resetPasswordToken = $resetPasswordTokenDao->getById($notification->getResetPasswordTokenId());

        $updatePassword->update(
            $resetPasswordToken,
            'foo',
            $password
        );
    }
)
    ->with([['foo.bar@baz.com', 'foobarfoo']])
    ->throws(WrongResetPasswordToken::class);

it(
    'throws an exception if token expired',
    function (string $email, string $password): void {
        $resetPassword         = self::$container->get(ResetPassword::class);
        $updatePassword        = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        $notification       = $resetPassword->reset($email);
        $resetPasswordToken = $resetPasswordTokenDao->getById($notification->getResetPasswordTokenId());

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);
        $resetPasswordTokenDao->save($resetPasswordToken);

        $updatePassword->update(
            $resetPasswordToken,
            $notification->getPlainToken(),
            $password
        );
    }
)
    ->with([['foo.bar@baz.com', 'foobarfoo']])
    ->throws(ResetPasswordTokenExpired::class);

it(
    'throws an exception if invalid password',
    function (string $email, string $password): void {
        $resetPassword         = self::$container->get(ResetPassword::class);
        $updatePassword        = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        $notification       = $resetPassword->reset($email);
        $resetPasswordToken = $resetPasswordTokenDao->getById($notification->getResetPasswordTokenId());

        $updatePassword->update(
            $resetPasswordToken,
            $notification->getPlainToken(),
            $password
        );
    }
)
    ->with([
        // Blank password.
        ['foo.bar@baz.com', DummyValues::BLANK],
        // Password < 8.
        ['foo.bar@baz.com','foo'],
        // We do not test "@Assert\NotCompromisedPassword"
        // as it is disable when "APP_ENV = test".
        // See config/packages/test/validator.yaml.
    ])
    ->throws(InvalidPassword::class);
