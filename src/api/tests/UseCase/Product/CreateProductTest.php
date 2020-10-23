<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Dao\UserDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Company;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\ProductPicture;
use App\Domain\Model\User;
use App\Domain\Storage\ProductPictureStorage;
use App\Domain\Throwable\InvalidModel;
use App\Domain\Throwable\InvalidStorable;
use App\Tests\UseCase\DummyValues;
use App\UseCase\Product\CreateProduct;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);
    $productDao = self::$container->get(ProductDao::class);
    assert($productDao instanceof ProductDao);

    $merchant = new User(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );
    $userDao->save($merchant);

    $company = new Company(
        $merchant,
        'foo'
    );
    $company->setId('1');
    $companyDao->save($company);

    $product = new Product(
        $company,
        'bar',
        1
    );
    $productDao->save($product);
});

it(
    'creates a product',
    function (
        string $name,
        float $price
    ): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);

        $company = $companyDao->getById('1');
        $product = $createProduct->create(
            $name,
            $price,
            $company
        );

        assertEquals($name, $product->getName());
        assertEquals($price, $product->getPrice());
        assertEquals($company, $product->getCompany());
        assertNull($product->getPictures());
    }
)
    ->with([
        ['foo', 1],
        ['foo', 1.0],
    ])
    ->group('product');

it(
    'stores the pictures',
    function (): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        assert($productPictureStorage instanceof ProductPictureStorage);

        $product = $createProduct->create(
            'foo',
            1,
            $companyDao->getById('1'),
            ProductPicture::createAllFromPaths([
                dirname(__FILE__) . '/foo.png',
                dirname(__FILE__) . '/foo.jpg',
            ])
        );

        assertNotNull($product->getPictures());
        assertCount(2, $product->getPictures());

        foreach ($product->getPictures() as $picture) {
            assertTrue($productPictureStorage->fileExists($picture));
        }
    }
)
    ->group('product');

it(
    'throws an exception if invalid product picture',
    function (): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $productPictureStorage = self::$container->get(ProductPictureStorage::class);
        assert($productPictureStorage instanceof ProductPictureStorage);

        $storables = ProductPicture::createAllFromPaths([
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.txt',
        ]);

        $company = $companyDao->getById('1');
        $createProduct->create(
            'foo',
            1,
            $company,
            $storables
        );

        foreach ($storables as $storable) {
            assertFalse($productPictureStorage->fileExists(
                $storable->getFilename()
            ));
        }
    }
)
    ->throws(InvalidStorable::class)
    ->group('product');

it(
    'throws an exception if invalid product',
    function (
        string $name,
        float $price
    ): void {
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);

        $createProduct->create(
            $name,
            $price,
            $companyDao->getById('1')
        );
    }
)
    ->with([
        // Existing name.
        ['bar', 1],
        // Blank name.
        [DummyValues::BLANK, 1],
        // Name > 255.
        [DummyValues::CHAR256, 1],
        // Negative price.
        ['foo', -1],
        // No price.
        ['foo', 0],
    ])
    ->throws(InvalidModel::class)
    ->group('product');
