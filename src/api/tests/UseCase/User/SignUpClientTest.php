<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\User\SignUpClient\SignUp;

it(
    'creates a client user',
    function (
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ): void {
        $signUpClient = self::$container->get(SignUp::class);
        assert($signUpClient instanceof SignUp);

        $user = $signUpClient->signUp(
            $firstName,
            $lastName,
            $email,
            $locale
        );

        assertEquals(Role::CLIENT, $user->getRole());
    }
)
    ->with([
        ['Foo', 'Bar', 'foo.bar@baz.com', Locale::EN],
    ]);
