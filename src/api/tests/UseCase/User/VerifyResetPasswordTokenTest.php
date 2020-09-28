<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\UseCase\User\VerifyResetPasswordToken\InvalidResetPasswordTokenId;
use App\UseCase\User\VerifyResetPasswordToken\ResetPasswordTokenExpired;
use App\UseCase\User\VerifyResetPasswordToken\VerifyResetPasswordToken;
use App\UseCase\User\VerifyResetPasswordToken\WrongResetPasswordToken;
use Safe\DateTimeImmutable;

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
        Locale::EN(),
        Role::MERCHANT()
    );
    $userDao->save($user);

    $validUntil = new DateTimeImmutable();
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
    'returns true if valid reset password token',
    function (): void {
        $verifyResetPasswordToken = self::$container->get(VerifyResetPasswordToken::class);
        assert($verifyResetPasswordToken instanceof VerifyResetPasswordToken);

        $result = $verifyResetPasswordToken->verifyResetPasswordToken(
            '1',
            'foo'
        );

        assertTrue($result);
    }
)
    ->group('user');

it(
    'throws an exception if invalid reset password token id',
    function (): void {
        $verifyResetPasswordToken = self::$container->get(VerifyResetPasswordToken::class);
        assert($verifyResetPasswordToken instanceof VerifyResetPasswordToken);

        $verifyResetPasswordToken->verifyResetPasswordToken(
            'foo',
            'foo'
        );
    }
)
    ->throws(InvalidResetPasswordTokenId::class)
    ->group('user');

it(
    'throws an exception if wrong token',
    function (): void {
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);
        $verifyResetPasswordToken = self::$container->get(VerifyResetPasswordToken::class);
        assert($verifyResetPasswordToken instanceof VerifyResetPasswordToken);

        $verifyResetPasswordToken->verifyResetPasswordToken(
            '1',
            'bar'
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
        $verifyResetPasswordToken = self::$container->get(VerifyResetPasswordToken::class);
        assert($verifyResetPasswordToken instanceof VerifyResetPasswordToken);

        $resetPasswordToken = $resetPasswordTokenDao->getById('1');

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);
        $resetPasswordTokenDao->save($resetPasswordToken);

        $verifyResetPasswordToken->verifyResetPasswordToken(
            '1',
            'foo'
        );
    }
)
    ->throws(ResetPasswordTokenExpired::class)
    ->group('user');
