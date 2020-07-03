<?php

declare(strict_types=1);

use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Enum\Filter\UsersSortBy;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\User;
use App\Domain\Throwable\Invalid\InvalidUsersFilters;
use App\UseCase\User\CreateUser;
use App\UseCase\User\GetUsers;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);

    $createUser->createUser(
        'A',
        'A',
        'a.a@a.a',
        Locale::EN,
        Role::ADMINISTRATOR
    );

    $createUser->createUser(
        'B',
        'B',
        'b.b@b.b',
        Locale::EN,
        Role::COMPANY
    );

    $createUser->createUser(
        'c',
        'c',
        'c.c@c.c',
        Locale::EN,
        Role::CLIENT
    );
});

it(
    'finds all users',
    function (): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users();
        assertCount(3, $result);
    }
);

it(
    'filters users with a generic search',
    function (string $search): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users($search);
        assertCount(1, $result);

        $user = $result->first();
        assert($user instanceof User);
        assertStringContainsStringIgnoringCase($search, $user->getFirstName());
        assertStringContainsStringIgnoringCase($search, $user->getLastName());
        assertStringContainsStringIgnoringCase($search, $user->getEmail());
    }
)
    ->with(['a', 'b', 'c']);

it(
    'filters users by role',
    function (string $role): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users(null, $role);
        assertCount(1, $result);

        $user = $result->first();
        assert($user instanceof User);
        assertEquals($role, $user->getRole());
    }
)
    ->with([Role::ADMINISTRATOR, Role::COMPANY, Role::CLIENT]);

it(
    'sorts users by first name',
    function (string $sortOrder): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users(null, null, UsersSortBy::FIRST_NAME, $sortOrder);
        assertCount(3, $result);

        /** @var User[] $users */
        $users = $result->toArray();
        if ($sortOrder === SortOrder::ASC) {
            assertStringContainsStringIgnoringCase('a', $users[0]->getFirstName());
            assertStringContainsStringIgnoringCase('b', $users[1]->getFirstName());
            assertStringContainsStringIgnoringCase('c', $users[2]->getFirstName());
        } else {
            assertStringContainsStringIgnoringCase('a', $users[2]->getFirstName());
            assertStringContainsStringIgnoringCase('b', $users[1]->getFirstName());
            assertStringContainsStringIgnoringCase('c', $users[0]->getFirstName());
        }
    }
)
    ->with([SortOrder::ASC, SortOrder::DESC]);

it(
    'sorts users by last name',
    function (string $sortOrder): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users(null, null, UsersSortBy::LAST_NAME, $sortOrder);
        assertCount(3, $result);

        /** @var User[] $users */
        $users = $result->toArray();
        if ($sortOrder === SortOrder::ASC) {
            assertStringContainsStringIgnoringCase('a', $users[0]->getLastName());
            assertStringContainsStringIgnoringCase('b', $users[1]->getLastName());
            assertStringContainsStringIgnoringCase('c', $users[2]->getLastName());
        } else {
            assertStringContainsStringIgnoringCase('a', $users[2]->getLastName());
            assertStringContainsStringIgnoringCase('b', $users[1]->getLastName());
            assertStringContainsStringIgnoringCase('c', $users[0]->getLastName());
        }
    }
)
    ->with([SortOrder::ASC, SortOrder::DESC]);

it(
    'sorts users by e-mail',
    function (string $sortOrder): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users(null, null, UsersSortBy::EMAIL, $sortOrder);
        assertCount(3, $result);

        /** @var User[] $users */
        $users = $result->toArray();
        if ($sortOrder === SortOrder::ASC) {
            assertStringContainsStringIgnoringCase('a', $users[0]->getEmail());
            assertStringContainsStringIgnoringCase('b', $users[1]->getEmail());
            assertStringContainsStringIgnoringCase('c', $users[2]->getEmail());
        } else {
            assertStringContainsStringIgnoringCase('a', $users[2]->getEmail());
            assertStringContainsStringIgnoringCase('b', $users[1]->getEmail());
            assertStringContainsStringIgnoringCase('c', $users[0]->getEmail());
        }
    }
)
    ->with([SortOrder::ASC, SortOrder::DESC]);

it(
    'sorts users by role',
    function (string $sortOrder): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $result = $getUsers->users(null, null, UsersSortBy::ROLE, $sortOrder);
        assertCount(3, $result);

        /** @var User[] $users */
        $users = $result->toArray();
        if ($sortOrder === SortOrder::ASC) {
            assertEquals(Role::ADMINISTRATOR, $users[0]->getRole());
            assertEquals(Role::CLIENT, $users[1]->getRole());
            assertEquals(Role::COMPANY, $users[2]->getRole());
        } else {
            assertEquals(Role::ADMINISTRATOR, $users[2]->getRole());
            assertEquals(Role::CLIENT, $users[1]->getRole());
            assertEquals(Role::COMPANY, $users[0]->getRole());
        }
    }
)
    ->with([SortOrder::ASC, SortOrder::DESC]);

it(
    'throws an exception if invalid filters',
    function (string $role, string $sortBy, string $sortOrder): void {
        $getUsers = self::$container->get(GetUsers::class);
        assert($getUsers instanceof GetUsers);

        $getUsers->users(null, $role, $sortBy, $sortOrder);
    }
)
    ->with([
        // Invalid role.
        ['foo', UsersSortBy::FIRST_NAME, SortOrder::ASC],
        // Invalid sort by.
        [Role::ADMINISTRATOR, 'foo', SortOrder::ASC],
        // Invalid sort order.
        [Role::ADMINISTRATOR, UsersSortBy::FIRST_NAME, 'foo'],
    ])
    ->throws(InvalidUsersFilters::class);
