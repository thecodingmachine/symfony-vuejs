<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Throwable\InvalidModel;
use App\Tests\UseCase\AsyncTransport;
use App\Tests\UseCase\DummyValues;
use App\UseCase\User\CreateUser;
use App\UseCase\User\ResetPassword\ResetPasswordNotification;
use App\UseCase\User\ResetPassword\ResetPasswordTask;
use App\UseCase\User\ResetPassword\ResetPasswordTaskHandler;
use App\UseCase\User\UpdatePassword\ResetPasswordTokenExpired;
use App\UseCase\User\UpdatePassword\UpdatePassword;
use App\UseCase\User\UpdatePassword\WrongResetPasswordToken;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $transport = self::$container->get(AsyncTransport::KEY);
    assert($transport instanceof InMemoryTransport);
    $resetPasswordTaskHandler = self::$container->get(ResetPasswordTaskHandler::class);
    assert($resetPasswordTaskHandler instanceof ResetPasswordTaskHandler);

    $createUser->createUser(
        'foo',
        'bar',
        'foo@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );

    assertCount(1, $transport->getSent());
    $envelope = $transport->get()[0];
    $message  = $envelope->getMessage();
    assert($message instanceof ResetPasswordTask);
    $resetPasswordTaskHandler($message);
});

it(
    'updates the password and deletes the token',
    function (): void {
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);

        $resetPasswordToken = $resetPasswordTokenDao->getById($message->getResetPasswordTokenId());
        $user               = $resetPasswordToken->getUser();

        $updatePassword->updatePassword(
            $resetPasswordToken,
            $message->getPlainToken(),
            'foobarfoo',
            'foobarfoo'
        );

        assertTrue(password_verify('foobarfoo', $user->getPassword()));
        $resetPasswordTokenDao->getById($message->getResetPasswordTokenId());
    }
)
    ->throws(TDBMException::class)
    ->group('user');

it(
    'throws an exception if wrong token',
    function (): void {
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);

        $resetPasswordToken = $resetPasswordTokenDao->getById($message->getResetPasswordTokenId());

        $updatePassword->updatePassword(
            $resetPasswordToken,
            'foo',
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
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);

        $resetPasswordToken = $resetPasswordTokenDao->getById($message->getResetPasswordTokenId());

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);
        $resetPasswordTokenDao->save($resetPasswordToken);

        $updatePassword->updatePassword(
            $resetPasswordToken,
            $message->getPlainToken(),
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
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $updatePassword = self::$container->get(UpdatePassword::class);
        assert($updatePassword instanceof UpdatePassword);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof  ResetPasswordTokenDao);

        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);

        $resetPasswordToken = $resetPasswordTokenDao->getById($message->getResetPasswordTokenId());

        $updatePassword->updatePassword(
            $resetPasswordToken,
            $message->getPlainToken(),
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
