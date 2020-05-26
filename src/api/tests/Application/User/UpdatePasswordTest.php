<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\ResetPassword\ResetPassword;
use App\Application\User\UpdatePassword\ResetPasswordTokenExpired;
use App\Application\User\UpdatePassword\UpdatePassword;
use App\Application\User\UpdatePassword\WrongResetPasswordToken;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Invalid\InvalidPassword;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Application\DummyValues;
use DateInterval;
use Safe\DateTimeImmutable;
use function assert;
use function password_verify;

final class UpdatePasswordTest extends ApplicationTestCase
{
    private const EMAIL = 'foo.bar@baz.com';
    private ResetPassword $resetPassword;
    private UpdatePassword $updatePassword;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;

    protected function setUp() : void
    {
        parent::setUp();
        $this->resetPassword                = self::$container->get(ResetPassword::class);
        $this->updatePassword               = self::$container->get(UpdatePassword::class);
        $this->resetPasswordTokenRepository = self::$container->get(ResetPasswordTokenRepository::class);
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

    public function testUpdatePassword() : void
    {
        $notification = $this->resetPassword->reset(self::EMAIL);
        $password     = 'foobarfoo';

        $user = $this->updatePassword->update(
            $notification->getResetPasswordTokenId(),
            $notification->getPlainToken(),
            $password
        );

        $this->assertTrue(password_verify($password, $user->getPassword()));

        $this->expectException(ResetPasswordTokenNotFoundById::class);
        $this->resetPasswordTokenRepository->mustFindOneById($notification->getResetPasswordTokenId());
    }

    public function testUpdatePasswordWithNonExistingTokenId() : void
    {
        $notification = $this->resetPassword->reset(self::EMAIL);

        $this->expectException(ResetPasswordTokenNotFoundById::class);
        $this->updatePassword->update(
            'foo',
            $notification->getPlainToken(),
            'foobarfoo'
        );
    }

    public function testUpdatePasswordWithWrongToken() : void
    {
        $notification = $this->resetPassword->reset(self::EMAIL);

        $this->expectException(WrongResetPasswordToken::class);
        $this->updatePassword->update(
            $notification->getResetPasswordTokenId(),
            'foo',
            'foobarfoo'
        );
    }

    public function testUpdatePasswordWithExpiredToken() : void
    {
        $notification       = $this->resetPassword->reset(self::EMAIL);
        $resetPasswordToken = $this->resetPasswordTokenRepository->mustFindOneById($notification->getResetPasswordTokenId());

        $validUntil = new DateTimeImmutable();
        $validUntil = $validUntil->sub(new DateInterval('P1D'));
        $resetPasswordToken->setValidUntil($validUntil);

        $this->resetPasswordTokenRepository->save($resetPasswordToken);

        $this->expectException(ResetPasswordTokenExpired::class);
        $this->updatePassword->update(
            $notification->getResetPasswordTokenId(),
            $notification->getPlainToken(),
            'foobarfoo'
        );
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testUpdatePasswordWithModelViolations(string $password) : void
    {
        $notification = $this->resetPassword->reset(self::EMAIL);

        $this->expectException(InvalidPassword::class);
        $this->updatePassword->update(
            $notification->getResetPasswordTokenId(),
            $notification->getPlainToken(),
            $password
        );
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function invalidDataProvider() : array
    {
        // We do not test "@Assert\NotCompromisedPassword"
        // as it is disable in when "APP_ENV = test".
        // See config/packages/test/validator.yaml.
        return [
            'Update with blank password' => [
                'password' => DummyValues::BLANK,
            ],
            'Update with password < 8' => ['password' => 'foo'],
        ];
    }
}
