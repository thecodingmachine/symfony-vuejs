<?php

declare(strict_types=1);

use App\Application\User\CreateUser;
use App\Application\User\ResetPassword\ResetPassword;
use App\Application\User\UpdatePassword\ResetPasswordTokenExpired;
use App\Application\User\UpdatePassword\UpdatePassword;
use App\Application\User\UpdatePassword\WrongResetPasswordToken;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use App\Tests\Application\DummyValues;
use Safe\DateTimeImmutable;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);

    $createUser->create(
        'Foo',
        'Bar',
        'foo.bar@baz.com',
        LocaleEnum::EN,
        RoleEnum::ADMINISTRATOR
    );
});

it(
    'updates the password and deletes the token',
    function (string $email, string $password): void {
        $resetPassword                = self::$container->get(ResetPassword::class);
        $updatePassword               = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenRepository instanceof  ResetPasswordTokenRepository);

        $notification = $resetPassword->reset($email);
        $user         = $updatePassword->update(
            $notification->getResetPasswordTokenId(),
            $notification->getPlainToken(),
            $password
        );

        assertTrue(password_verify($password, $user->getPassword()));
        $resetPasswordTokenRepository->mustFindOneById($notification->getResetPasswordTokenId());
    }
)
    ->throws(ResetPasswordTokenNotFoundById::class)
    ->with([['foo.bar@baz.com', 'foobarfoo']]);

it(
    'throws an exception if the token does not exist',
    function (string $email, string $password): void {
        $resetPassword  = self::$container->get(ResetPassword::class);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);

        $notification = $resetPassword->reset($email);
        $updatePassword->update(
            'foo',
            $notification->getPlainToken(),
            $password
        );
    }
)
    ->throws(ResetPasswordTokenNotFoundById::class)
    ->with([['foo.bar@baz.com', 'foobarfoo']]);

it(
    'throws an exception if wrong token',
    function (string $email, string $password): void {
        $resetPassword  = self::$container->get(ResetPassword::class);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);

        $notification = $resetPassword->reset($email);
        $updatePassword->update(
            $notification->getResetPasswordTokenId(),
            'foo',
            $password
        );
    }
)
    ->throws(WrongResetPasswordToken::class)
    ->with([['foo.bar@baz.com', 'foobarfoo']]);

it(
    'throws an exception if token expired',
    function (string $email, string $password): void {
        $resetPassword                = self::$container->get(ResetPassword::class);
        $updatePassword               = self::$container->get(UpdatePassword::class);
        $resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);
        assert($resetPasswordTokenRepository instanceof  ResetPasswordTokenRepository);

        $notification       = $resetPassword->reset($email);
        $resetPasswordToken = $resetPasswordTokenRepository->mustFindOneById($notification->getResetPasswordTokenId());

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);
        $resetPasswordTokenRepository->save($resetPasswordToken);

        $updatePassword->update(
            $notification->getResetPasswordTokenId(),
            $notification->getPlainToken(),
            $password
        );
    }
)
    ->throws(ResetPasswordTokenExpired::class)
    ->with([['foo.bar@baz.com', 'foobarfoo']]);

it(
    'throws an exception if invalid password',
    function (string $email, string $password): void {
        $resetPassword  = self::$container->get(ResetPassword::class);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($resetPassword instanceof ResetPassword);
        assert($updatePassword instanceof UpdatePassword);

        $notification = $resetPassword->reset($email);
        $updatePassword->update(
            $notification->getResetPasswordTokenId(),
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
