<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\Domain\Model\Storable\ProductPicture;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProduct;
use App\UseCase\Product\DeleteProductsPictures\DeleteProductsPicturesTask;
use App\UseCase\User\CreateUser;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

beforeEach(function (): void {
    $createUser = self::$container->get(CreateUser::class);
    assert($createUser instanceof CreateUser);
    $createCompany = self::$container->get(CreateCompany::class);
    assert($createCompany instanceof CreateCompany);
    $companyDao = self::$container->get(CompanyDao::class);
    assert($companyDao instanceof CompanyDao);

    $merchant = $createUser->createUser(
        'foo',
        'bar',
        'merchant@foo.com',
        Locale::EN(),
        Role::MERCHANT()
    );

    $company = $createCompany->createCompany(
        $merchant,
        'foo'
    );
    $company->setId('1');
    $companyDao->save($company);
});

it(
    'deletes the product',
    function (): void {
        $createProduct = self::$container->get(CreateProduct::class);
        assert($createProduct instanceof CreateProduct);
        $companyDao = self::$container->get(CompanyDao::class);
        assert($companyDao instanceof CompanyDao);
        $deleteProduct = self::$container->get(DeleteProduct::class);
        assert($deleteProduct instanceof DeleteProduct);
        $productDao = self::$container->get(ProductDao::class);
        assert($productDao instanceof ProductDao);

        $product = $createProduct->create(
            'foo',
            1,
            $companyDao->getById('1')
        );

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

        // There should be two messages: one from CreateUser
        // use case called in the beforeEach function and one
        // for deleting the product's pictures.
        assertCount(2, $transport->getSent());

        $envelope = $transport->get()[1];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteProductsPicturesTask);

        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class)
    ->group('product');
