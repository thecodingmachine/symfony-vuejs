<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\User\ResetPassword\ResetPassword;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

use function PHPUnit\Framework\assertCount;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);

    $user = new User(
        'foo',
        'bar',
        'merchant@foo.com',
        strval(Locale::EN()),
        strval(Role::MERCHANT())
    );
    $userDao->save($user);
});

it(
    'dispatches an email',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);

        $resetPassword->resetPassword($email);
        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof SendEmailMessage);
    }
)
    ->with(['merchant@foo.com'])
    ->group('user');

it(
    'does not dispatch an email if email does not exist',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);

        $resetPassword->resetPassword($email);
        assertCount(0, $transport->getSent());
    }
)
    ->with(['foo'])
    ->group('user');

it(
    'deletes the previous token',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        assert($resetPassword instanceof ResetPassword);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        $resetPassword->resetPassword($email);
        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof SendEmailMessage);

        $firstResetPasswordToken = $resetPasswordTokenDao->findAll()->first();
        assert($firstResetPasswordToken instanceof ResetPasswordToken);

        $resetPassword->resetPassword($email);
        assertCount(2, $transport->getSent());
        $envelope = $transport->get()[1];
        $message1 = $envelope->getMessage();
        assert($message1 instanceof SendEmailMessage);

        $resetPasswordTokenDao->getById($firstResetPasswordToken->getId());
    }
)
    ->with(['merchant@foo.com'])
    ->throws(TDBMException::class)
    ->group('user');
