<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\ResetPassword\ResetPassword;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use App\Domain\Throwable\NotFound\UserNotFoundByEmail;
use App\Tests\Application\ApplicationTestCase;
use Symfony\Component\Messenger\Transport\TransportInterface;
use function assert;

final class ResetPasswordTest extends ApplicationTestCase
{
    private const EMAIL = 'foo.bar@baz.com';
    private ResetPassword $resetPassword;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private TransportInterface $transport;

    protected function setUp() : void
    {
        parent::setUp();
        $this->resetPassword                = self::$container->get(ResetPassword::class);
        $this->resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
        $this->transport                    = self::$container->get('messenger.transport.async');
        $userRepository                     = self::$container->get(UserRepository::class);
        assert($userRepository instanceof UserRepository);

        $user = new User(
            'Foo',
            'Bar',
            self::EMAIL,
            LocaleEnum::EN,
            RoleEnum::ADMINISTRATOR
        );
        $userRepository->save($user);
    }

    public function testResetPassword() : void
    {
        $this->resetPassword->reset(self::EMAIL);
        $this->assertCount(1, $this->transport->getSent());
    }

    public function testResetPasswordFromNonExistingUser() : void
    {
        $this->expectException(UserNotFoundByEmail::class);
        $this->resetPassword->reset('foo');
        $this->assertCount(0, $this->transport->getSent());
    }

    public function testResetPasswordTwice() : void
    {
        $firstNotification = $this->resetPassword->reset(self::EMAIL);
        $this->resetPassword->reset(self::EMAIL);

        $this->expectException(ResetPasswordTokenNotFoundById::class);
        $this->resetPasswordTokenRepository->mustFindOneById($firstNotification->getResetPasswordTokenId());

        $this->assertCount(2, $this->transport->getSent());
    }
}
