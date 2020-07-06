<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\User\ResetPassword\ResetPassword;
use App\UseCase\User\ResetPassword\ResetPasswordNotification;
use App\UseCase\User\ResetPassword\ResetPasswordTask;
use App\UseCase\User\ResetPassword\ResetPasswordTaskHandler;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);

    // We do not use the CreateUser use case
    // as it dispatches the same messages as the
    // ones we want to test.
    $user = new User(
        'foo',
        'bar',
        'foo@foo.com',
        strval(Locale::EN()),
        strval(Role::MERCHANT())
    );
    $userDao->save($user);
});

it(
    'dispatches a task; when handled dispatches a notification',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $resetPasswordTaskHandler = self::$container->get(ResetPasswordTaskHandler::class);
        assert($resetPasswordTaskHandler instanceof ResetPasswordTaskHandler);

        $resetPassword->resetPassword($email);
        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordTask);

        $resetPasswordTaskHandler($message);
        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);
    }
)
    ->with(['foo@foo.com'])
    ->group('user');

it(
    'dispatches a task with a non-existing e-mail; when handled does not dispatch a notification',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $resetPasswordTaskHandler = self::$container->get(ResetPasswordTaskHandler::class);
        assert($resetPasswordTaskHandler instanceof ResetPasswordTaskHandler);

        $resetPassword->resetPassword($email);
        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordTask);

        $resetPasswordTaskHandler($message);
        assertCount(1, $transport->getSent());
    }
)
    ->with(['foo'])
    ->group('user');

it(
    'dispatches a task; when handled twice deletes the previous token',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $resetPasswordTaskHandler = self::$container->get(ResetPasswordTaskHandler::class);
        assert($resetPasswordTaskHandler instanceof ResetPasswordTaskHandler);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        $resetPassword->resetPassword($email);
        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordTask);

        $resetPasswordTaskHandler($message);
        $resetPasswordTaskHandler($message);

        assertCount(3, $transport->getSent());
        $envelope = $transport->get()[1];
        $message1 = $envelope->getMessage();
        assert($message1 instanceof ResetPasswordNotification);
        $envelope = $transport->get()[2];
        $message2 = $envelope->getMessage();
        assert($message2 instanceof ResetPasswordNotification);

        $resetPasswordTokenDao->getById($message1->getResetPasswordTokenId());
    }
)
    ->with(['foo@foo.com'])
    ->throws(TDBMException::class)
    ->group('user');
