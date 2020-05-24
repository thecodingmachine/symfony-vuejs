<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\SignUpClient;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Tests\Application\ApplicationTestCase;

final class SignUpClientTest extends ApplicationTestCase
{
    private SignUpClient $signUpClient;

    protected function setUp() : void
    {
        parent::setUp();
        $this->signUpClient = self::$container->get(SignUpClient::class);
    }

    public function testSignUp() : void
    {
        $user = $this->signUpClient->signUp(
            'Foo',
            'Bar',
            'foo.bar@baz.com',
            LocaleEnum::EN,
        );

        $this->assertEquals(RoleEnum::CLIENT, $user->getRole());
    }
}
