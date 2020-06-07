<?php

declare(strict_types=1);

use App\Application\User\SearchUsers;
use App\Domain\Enum\Filter\SortOrderEnum;
use App\Domain\Enum\Filter\UsersSortByEnum;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;
use App\Domain\Throwable\Invalid\InvalidUsersFilters;

beforeEach(function (): void {
    $userRepository = self::$container->get(UserRepository::class);
    assert($userRepository instanceof UserRepository);

    $user = new User(
        'A',
        'A',
        'a.a@a.a',
        LocaleEnum::EN,
        RoleEnum::ADMINISTRATOR
    );
    $userRepository->save($user);

    $user = new User(
        'B',
        'B',
        'b.b@b.b',
        LocaleEnum::EN,
        RoleEnum::COMPANY
    );
    $userRepository->save($user);

    $user = new User(
        'c',
        'c',
        'c.c@c.c',
        LocaleEnum::EN,
        RoleEnum::CLIENT
    );
    $userRepository->save($user);
});

it(
    'finds all users',
    function (): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search();
        assertCount(3, $users);
    }
);

it(
    'filters users with a generic search',
    function (string $search): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search($search);
        assertCount(1, $users);

        $user = $users->first();
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
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search(null, $role);
        assertCount(1, $users);

        $user = $users->first();
        assert($user instanceof User);
        assertEquals($role, $user->getRole());
    }
)
    ->with([RoleEnum::ADMINISTRATOR, RoleEnum::COMPANY, RoleEnum::CLIENT]);

it(
    'sorts users by first name',
    function (string $sortOrder): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search(null, null, UsersSortByEnum::FIRST_NAME, $sortOrder);
        assertCount(3, $users);

        /** @var User[] $users */
        $users = $users->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
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
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'sorts users by last name',
    function (string $sortOrder): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search(null, null, UsersSortByEnum::LAST_NAME, $sortOrder);
        assertCount(3, $users);

        /** @var User[] $users */
        $users = $users->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
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
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'sorts users by e-mail',
    function (string $sortOrder): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search(null, null, UsersSortByEnum::EMAIL, $sortOrder);
        assertCount(3, $users);

        /** @var User[] $users */
        $users = $users->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
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
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'sorts users by role',
    function (string $sortOrder): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $users = $searchUsers->search(null, null, UsersSortByEnum::ROLE, $sortOrder);
        assertCount(3, $users);

        /** @var User[] $users */
        $users = $users->toArray();
        if ($sortOrder === SortOrderEnum::ASC) {
            assertEquals(RoleEnum::ADMINISTRATOR, $users[0]->getRole());
            assertEquals(RoleEnum::CLIENT, $users[1]->getRole());
            assertEquals(RoleEnum::COMPANY, $users[2]->getRole());
        } else {
            assertEquals(RoleEnum::ADMINISTRATOR, $users[2]->getRole());
            assertEquals(RoleEnum::CLIENT, $users[1]->getRole());
            assertEquals(RoleEnum::COMPANY, $users[0]->getRole());
        }
    }
)
    ->with([SortOrderEnum::ASC, SortOrderEnum::DESC]);

it(
    'throws an exception if invalid filters',
    function (string $role, string $sortBy, string $sortOrder): void {
        $searchUsers = self::$container->get(SearchUsers::class);
        assert($searchUsers instanceof SearchUsers);

        $searchUsers->search(null, $role, $sortBy, $sortOrder);
    }
)
    ->with([
        // Invalid role.
        ['foo', UsersSortByEnum::FIRST_NAME, SortOrderEnum::ASC],
        // Invalid sort by.
        [RoleEnum::ADMINISTRATOR, 'foo', SortOrderEnum::ASC],
        // Invalid sort order.
        [RoleEnum::ADMINISTRATOR, UsersSortByEnum::FIRST_NAME, 'foo'],
    ])
    ->throws(InvalidUsersFilters::class);
