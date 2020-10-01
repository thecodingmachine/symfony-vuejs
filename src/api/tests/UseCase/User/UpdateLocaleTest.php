<?php

declare(strict_types=1);

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\User\CreateUser;
use App\UseCase\User\UpdateLocale;

use function PHPUnit\Framework\assertEquals;

it(
    'updates the locale',
    function (): void {
        $createUser = self::$container->get(CreateUser::class);
        assert($createUser instanceof CreateUser);
        $updateLocale = self::$container->get(UpdateLocale::class);
        assert($updateLocale instanceof UpdateLocale);

        $user = $createUser->createUser(
            'foo',
            'bar',
            'foo.bar@baz.com',
            Locale::EN(),
            Role::ADMINISTRATOR()
        );

        $updateLocale->updateLocale($user, Locale::FR());

        assertEquals(Locale::FR(), $user->getLocale());
    }
)
    ->group('user');
