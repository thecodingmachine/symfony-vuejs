<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ResetPasswordTokenDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\Company\CreateCompany;
use App\UseCase\User\CreateUser;
use App\UseCase\User\DeleteUser;
use App\UseCase\User\ResetPassword\ResetPasswordTask;
use App\UseCase\User\ResetPassword\ResetPasswordTaskHandler;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $transport = self::$container->get(AsyncTransport::KEY);
    assert($transport instanceof InMemoryTransport);
    $resetPasswordTaskHandler = self::$container->get(ResetPasswordTaskHandler::class);
    assert($resetPasswordTaskHandler instanceof ResetPasswordTaskHandler);

    $user = $createUser->createUser(
        'foo',
        'bar',
        'foo@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );
    $user->setId('1');
    $userDao->save($user);

    assertCount(1, $transport->getSent());
    $envelope = $transport->get()[0];
    $message  = $envelope->getMessage();
    assert($message instanceof ResetPasswordTask);
    $resetPasswordTaskHandler($message);
});

it(
    'deletes the user',
    function (): void {
        $deleteUser = self::$container->get(DeleteUser::class);
        assert($deleteUser instanceof DeleteUser);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        $user = $userDao->getById('1');
        $deleteUser->deleteUser($user);

        $userDao->getById($user->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('user');

it(
    'deletes the reset password token',
    function (): void {
        $deleteUser = self::$container->get(DeleteUser::class);
        assert($deleteUser instanceof DeleteUser);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $resetPasswordTokenDao = self::$container->get(ResetPasswordTokenDao::class);
        assert($resetPasswordTokenDao instanceof ResetPasswordTokenDao);

        assertCount(1, $resetPasswordTokenDao->findAll());

        $user = $userDao->getById('1');
        $deleteUser->deleteUser($user);

        assertCount(0, $resetPasswordTokenDao->findAll());
        $resetPasswordTokenDao->getById($user->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('user');

it(
    "deletes the merchant's companies",
    function (): void {
        $deleteUser = self::$container->get(DeleteUser::class);
        assert($deleteUser instanceof DeleteUser);
        $userDao = self::$container->get(UserDao::class);
        assert($userDao instanceof UserDao);
        $createCompany = self::$container->get(CreateCompany::class);
        assert($createCompany instanceof CreateCompany);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);

        $merchant = $userDao->getById('1');
        $company  = $createCompany->createCompany(
            $merchant,
            'foo'
        );

        $deleteUser->deleteUser($merchant);

        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('user');
