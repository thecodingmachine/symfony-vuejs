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
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProduct;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $userDao = self::$container->get(UserDao::class);
    assert($userDao instanceof UserDao);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);

    $merchant = new User(
        'foo',
        'bar',
        'merchant@foo.com',
        strval(Locale::EN()),
        strval(Role::MERCHANT())
    );
    $userDao->save($merchant);

    $company = new Company(
        $merchant,
        'foo'
    );
    $company->setId('1');
    $companyDao->save($company);
});

it(
    'deletes the product',
    function (): void {
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $deleteProduct = self::$container->get(DeleteProduct::class);
        assert($deleteProduct instanceof DeleteProduct);

        $product = new Product(
            $companyDao->getById('1'),
            'foo',
            1
        );
        $productDao->save($product);

        $deleteProduct->deleteProduct($product);
        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('product');

it(
    "sends a task for deleting the product's pictures",
    function (): void {
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $deleteProduct = self::$container->get(DeleteProduct::class);
        assert($deleteProduct instanceof DeleteProduct);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);
        $transport = self::$container->get(AsyncTransport::KEY);
        assert($transport instanceof InMemoryTransport);

        $product = $createProduct->create(
            'foo',
            1,
            $companyDao->getById('1'),
            ProductPicture::createAllFromPaths([
                dirname(__FILE__) . '/foo.png',
                dirname(__FILE__) . '/foo.jpg',
            ])
        );

        $deleteProduct->deleteProduct($product);

        assertCount(1, $transport->getSent());
        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteProductsPicturesTask);

        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('product');
