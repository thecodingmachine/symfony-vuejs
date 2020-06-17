<?php

declare(strict_types=1);

use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\UseCase\User\SignUpClient\SignUpClient;

it(
    'creates a client user',
    function (
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ): void {
        $signUpClient = self::$container->get(SignUpClient::class);
        assert($signUpClient instanceof SignUpClient);

        $user = $signUpClient->signUp(
            $firstName,
            $lastName,
            $email,
            $locale
        );

        assertEquals(RoleEnum::CLIENT, $user->getRole());
    }
)
    ->with([
        ['Foo', 'Bar', 'foo.bar@baz.com', LocaleEnum::EN],
    ]);
