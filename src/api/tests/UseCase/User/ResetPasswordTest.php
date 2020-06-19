<?php

declare(strict_types=1);

use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\User\ResetPassword\ResetPassword;
use App\UseCase\User\ResetPassword\ResetPasswordNotification;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);

    // We do not use the CreateUser use case
    // as we check the notifications in the following tests.
    $user = new User(
        'Foo',
        'Bar',
        'foo.bar@baz.com',
        LocaleEnum::EN,
        RoleEnum::ADMINISTRATOR
    );
    $userDao->save($user);
});

it(
    'dispatches a notification',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        assert($resetPassword instanceof ResetPassword);
        assert($transport instanceof InMemoryTransport);

        $resetPassword->reset($email);
        assertCount(1, $transport->getSent());

        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof ResetPasswordNotification);
    }
)
    ->with(['foo.bar@baz.com']);

it(
    'throws an exception if the e-mail is not associated to a user',
    function (string $email): void {
        $resetPassword = self::$container->get(ResetPassword::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        assert($resetPassword instanceof ResetPassword);
        assert($transport instanceof InMemoryTransport);

        $resetPassword->reset($email);
        assertCount(0, $transport->getSent());
    }
)
    ->with(['foo'])
    ->throws(UserNotFoundByEmail::class);

it(
    'deletes the previous token if called twice',
    function (string $email): void {
        $resetPassword         = self::$container->get(ResetPassword::class);
        $transport             = self::$container->get(AsyncTransport::KEY);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPassword instanceof ResetPassword);
        assert($transport instanceof InMemoryTransport);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        $firstNotification = $resetPassword->reset($email);
        $resetPassword->reset($email);

        assertCount(2, $transport->getSent());

        $envelopes = $transport->get();
        foreach ($envelopes as $envelope) {
            $message = $envelope->getMessage();
            assert($message instanceof ResetPasswordNotification);
        }

        $resetPasswordTokenDao->getById($firstNotification->getResetPasswordTokenId());
    }
)
    ->with(['foo.bar@baz.com'])
    ->throws(TDBMException::class);
