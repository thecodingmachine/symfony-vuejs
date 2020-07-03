<?php

declare(strict_types=1);

namespace App\Infrastructure\Fixtures;

use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Model\User;
use App\Domain\Throwable\InvalidModel;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\User\DeleteUser;

use function dirname;
use function strval;

final class AppFixtures extends Fixtures
{
    private UserDao $userDao;
    private DeleteUser $deleteUser;
    private CreateCompany $createCompany;
    private CreateProduct $createProduct;

    public function __construct(
        UserDao $userDao,
        DeleteUser $deleteUser,
        CreateCompany $createCompany,
        CreateProduct $createProduct
    ) {
        $this->userDao       = $userDao;
        $this->deleteUser    = $deleteUser;
        $this->createCompany = $createCompany;
        $this->createProduct = $createProduct;

        parent::__construct();
    }

    public function purge(): void
    {
        // When deleting a user, we actually
        // also delete the companies and the
        // products (and their pictures).
        $users = $this->userDao->findAll();
        foreach ($users as $user) {
            $this->deleteUser->deleteUser($user);
        }
    }

    /**
     * @throws InvalidModel
     */
    public function load(): void
    {
        $admin = new User(
            $this->faker->firstName,
            $this->faker->lastName,
            'admin@companies-and-products.localhost',
            strval(Locale::EN()),
            strval(Role::ADMINISTRATOR())
        );
        $admin->setPassword('foo');

        $merchant = new User(
            $this->faker->firstName,
            $this->faker->lastName,
            'merchant@companies-and-products.localhost',
            strval(Locale::EN()),
            strval(Role::MERCHANT())
        );
        $merchant->setPassword('foo');

        $client = new User(
            $this->faker->firstName,
            $this->faker->lastName,
            'client@companies-and-products.localhost',
            strval(Locale::EN()),
            strval(Role::CLIENT())
        );
        $client->setPassword('foo');

        $this->userDao->save($admin);
        $this->userDao->save($merchant);
        $this->userDao->save($client);

        for ($i = 0; $i < 5; $i++) {
            $company = $this->createCompany
                ->createCompany(
                    $merchant,
                    $this->faker->company,
                    $this->faker->url
                );

            for ($j = 0; $j < 10; $j++) {
                $this->createProduct
                    ->create(
                        $this->faker->domainWord . ' ' . $this->faker->colorName,
                        $this->faker->randomFloat(null, 1),
                        $company,
                        ProductPicture::createAllFromPaths([
                            dirname(__FILE__) . '/picture.png',
                            dirname(__FILE__) . '/picture.png',
                            dirname(__FILE__) . '/picture.png',
                            dirname(__FILE__) . '/picture.png',
                            dirname(__FILE__) . '/picture.png',
                        ])
                    );
            }
        }
    }
}
