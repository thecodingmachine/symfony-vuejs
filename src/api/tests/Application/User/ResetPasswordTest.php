<?php

declare(strict_types=1);

use App\Application\User\ResetPassword\ResetPassword;
use App\Application\User\ResetPassword\ResetPasswordNotification;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\Tests\AsyncTransport;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

beforeEach(function () : void {
    $userRepository = self::$container->get(UserRepository::class);
    assert($userRepository instanceof UserRepository);

    $user = new User(
        'Foo',
        'Bar',
        'foo.bar@baz.com',
        LocaleEnum::EN,
        RoleEnum::ADMINISTRATOR
    );
    $userRepository->save($user);
});

it(
    'dispatches a notification',
    function (string $email) : void {
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
    function (string $email) : void {
        $resetPassword = self::$container->get(ResetPassword::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        assert($resetPassword instanceof ResetPassword);
        assert($transport instanceof InMemoryTransport);

        $resetPassword->reset($email);
        assertCount(0, $transport->getSent());
    }
)
    ->throws(UserNotFoundByEmail::class)
    ->with(['foo']);

it(
    'deletes the previous token if called twice',
    function (string $email) : void {
        $resetPassword                = self::$container->get(ResetPassword::class);
        $transport                    = self::$container->get(AsyncTransport::KEY);
        $resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        assert($resetPassword instanceof ResetPassword);
        assert($transport instanceof InMemoryTransport);
        assert($resetPasswordTokenRepository instanceof ResetPasswordTokenRepository);

        $firstNotification = $resetPassword->reset($email);
        $resetPassword->reset($email);

        $resetPasswordTokenRepository->mustFindOneById($firstNotification->getResetPasswordTokenId());
        assertCount(2, $transport->getSent());

        $envelopes = $transport->get();
        foreach ($envelopes as $envelope) {
            $message = $envelope->getMessage();
            assert($message instanceof ResetPasswordNotification);
        }
    }
)
    ->throws(ResetPasswordTokenNotFoundById::class)
    ->with(['foo.bar@baz.com']);
