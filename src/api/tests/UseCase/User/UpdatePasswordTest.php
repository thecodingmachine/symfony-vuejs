<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use App\Tests\UseCase\DummyValues;
use App\UseCase\User\UpdatePassword\ResetPasswordTokenExpired;
use App\UseCase\User\UpdatePassword\UpdatePassword;
use App\UseCase\User\UpdatePassword\WrongResetPasswordToken;
use Safe\DateTimeImmutable;
use TheCodingMachine\TDBM\TDBMException;
use function PHPUnit\Framework\assertTrue;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
    assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

    $user = new User(
        'foo',
        'bar',
        'merchant@foo.com',
        strval(Locale::EN()),
        strval(Role::MERCHANT())
    );
    $userDao->save($user);

    $validUntil = new \DateTimeImmutable();
    $validUntil = $validUntil->add(new DateInterval('P1D')); // Add one day to current date time.

    $resetPasswordToken = new ResetPasswordToken(
        $user,
        'foo',
        $validUntil
    );
    $resetPasswordToken->setId('1');
    $resetPasswordTokenDao->save($resetPasswordToken);
});

it(
    'updates the password and deletes the token',
    function (): void {
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);

        $resetPasswordToken = $resetPasswordTokenDao->getById('1');
        $user               = $resetPasswordToken->getUser();

        $updatePassword->updatePassword(
            $resetPasswordToken,
            'foo',
            'foobarfoo',
            'foobarfoo'
        );

        assertTrue(password_verify('foobarfoo', $user->getPassword()));
        $resetPasswordTokenDao->getById($resetPasswordToken->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('user');

it(
    'throws an exception if wrong token',
    function (): void {
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);

        $resetPasswordToken = $resetPasswordTokenDao->getById('1');

        $updatePassword->updatePassword(
            $resetPasswordToken,
            'bar',
            'foobarfoo',
            'foobarfoo'
        );
    }
)
    ->throws(WrongResetPasswordToken::class)
    ->group('user');

it(
    'throws an exception if token expired',
    function (): void {
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);

        $resetPasswordToken = $resetPasswordTokenDao->getById('1');

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);
        $resetPasswordTokenDao->save($resetPasswordToken);

        $updatePassword->updatePassword(
            $resetPasswordToken,
            'foo',
            'foobarfoo',
            'foobarfoo'
        );
    }
)
    ->throws(ResetPasswordTokenExpired::class)
    ->group('user');

it(
    'throws an exception if invalid password',
    function (string $newPassword, string $passwordConfirmation): void {
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);

        $resetPasswordToken = $resetPasswordTokenDao->getById('1');

        $updatePassword->updatePassword(
            $resetPasswordToken,
            'foo',
            $newPassword,
            $passwordConfirmation
        );
    }
)
    ->with([
        // Blank password.
        [DummyValues::BLANK, DummyValues::BLANK],
        // Password < 8.
        ['foo', 'foo'],
        // Wrong password confirmation.
        ['foobarfoo', 'barfoobar'],
        // We do not test "@Assert\NotCompromisedPassword"
        // as it is disable when "APP_ENV = test".
        // See config/packages/test/validator.yaml.
    ])
    ->throws(InvalidModel::class)
    ->group('user');
