<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\CreateUser\CreateUser;
use App\Application\User\CreateUser\InvalidUser;
use App\Domain\Enum\LocaleEnum;
use App\Domain\Enum\RoleEnum;
use App\Domain\Throwable\Exist\UserWithEmailExist;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Application\DummyValues;

final class CreateUserTest extends ApplicationTestCase
{
    private CreateUser $createUser;

    protected function setUp() : void
    {
        parent::setUp();
        $this->createUser = self::$container->get(CreateUser::class);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testCreateUser(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ) : void {
        $user = $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );

        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($email, $user->getEmail());
        $this->assertNull($user->getPassword());
        $this->assertEquals($locale, $user->getLocale());
        $this->assertEquals($role, $user->getRole());
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function validDataProvider() : array
    {
        return [
            'Create an administrator user' => $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a company user'=> $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                LocaleEnum::EN,
                RoleEnum::COMPANY
            ),
            'Create a client user'=> $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                LocaleEnum::FR,
                RoleEnum::CLIENT
            ),
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testCreateUserWithModelViolations(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ) : void {
        $this->expectException(InvalidUser::class);
        $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function invalidDataProvider() : array
    {
        return [
            'Create a user with a blank first name' => $this->createArgs(
                DummyValues::BLANK,
                'Bar',
                'foo',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with a first name > 255' => $this->createArgs(
                DummyValues::CHAR256,
                'Bar',
                'foo',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with a blank last name' => $this->createArgs(
                'Foo',
                DummyValues::BLANK,
                'foo',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with a last name > 255' => $this->createArgs(
                'Foo',
                DummyValues::CHAR256,
                'foo',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with an invalid e-mail' => $this->createArgs(
                'Foo',
                'Bar',
                'foo',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with an invalid locale' => $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                'foo',
                RoleEnum::ADMINISTRATOR
            ),
            'Create a user with an invalid role' => $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                LocaleEnum::EN,
                'foo'
            ),
        ];
    }

    /**
     * @dataProvider duplicateDataProvider
     */
    public function testCreateExistingUser(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ) : void {
        $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
        $this->expectException(UserWithEmailExist::class);
        $this->createUser->create(
            $firstName,
            $lastName,
            $email,
            $locale,
            $role
        );
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function duplicateDataProvider() : array
    {
        return [
            'Create an existing user' => $this->createArgs(
                'Foo',
                'Bar',
                'foo.bar@baz.com',
                LocaleEnum::EN,
                RoleEnum::ADMINISTRATOR
            ),
        ];
    }

    /**
     * @return array<string,string>
     */
    private function createArgs(
        string $firstName,
        string $lastName,
        string $email,
        string $locale,
        string $role
    ) : array {
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'locale' => $locale,
            'role' => $role,
        ];
    }
}
