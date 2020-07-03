<?php

declare(strict_types=1);

use App\Domain\Dao\CompanyDao;
use App\Domain\Dao\ProductDao;
use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Model\Storable\ProductPicture;
use App\Tests\UseCase\AsyncTransport;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Company\DeleteCompany;
use App\UseCase\Company\DeleteCompanyLogo\DeleteCompaniesLogosTask;
use App\UseCase\Product\CreateProduct;
use App\UseCase\Product\DeleteProductPictures\DeleteProductPicturesTask;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use TheCodingMachine\TDBM\TDBMException;

it(
    'deletes the company',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        $companyDao    = self::$container->get(CompanyDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($deleteCompany instanceof DeleteCompany);
        assert($companyDao instanceof CompanyDao);

        $company = $createCompany->create('foo');
        $deleteCompany->deleteCompany($company);

        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class);

it(
    'deletes the associated product',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        $productDao    = self::$container->get(ProductDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($deleteCompany instanceof DeleteCompany);
        assert($productDao instanceof ProductDao);

        $company = $createCompany->create('foo');
        $product = $createProduct->create(
            'foo',
            1,
            $company,
            null
        );

        $deleteCompany->deleteCompany($company);
        $productDao->getById($product->getId());
    }
)
    ->throws(TDBMException::class);

it(
    'sends a task for deleting the logo',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        $companyDao    = self::$container->get(CompanyDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($deleteCompany instanceof DeleteCompany);
        assert($transport instanceof InMemoryTransport);
        assert($companyDao instanceof CompanyDao);

        $storable = CompanyLogo::createFromPath(
            dirname(__FILE__) . '/foo.jpg',
        );

        $company = $createCompany->create(
            'foo',
            null,
            $storable
        );

        $deleteCompany->deleteCompany($company);
        assertCount(1, $transport->getSent());

        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteCompaniesLogosTask);

        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class);

it(
    'sends a task for deleting the associated product pictures',
    function (): void {
        $createCompany = self::$container->get(CreateCompany::class);
        $createProduct = self::$container->get(CreateProduct::class);
        $deleteCompany = self::$container->get(DeleteCompany::class);
        $transport     = self::$container->get(AsyncTransport::KEY);
        $companyDao    = self::$container->get(CompanyDao::class);
        assert($createCompany instanceof CreateCompany);
        assert($createProduct instanceof CreateProduct);
        assert($deleteCompany instanceof DeleteCompany);
        assert($transport instanceof InMemoryTransport);
        assert($companyDao instanceof CompanyDao);

        $pictures = [
            dirname(__FILE__) . '/foo.png',
            dirname(__FILE__) . '/foo.jpg',
        ];

        $storables = ProductPicture::createAllFromPaths(
            $pictures
        );

        $company = $createCompany->create('foo');
        $createProduct->create(
            'foo',
            1,
            $company,
            $storables
        );

        $deleteCompany->deleteCompany($company);
        assertCount(1, $transport->getSent());

        $envelope = $transport->get()[0];
        $message  = $envelope->getMessage();
        assert($message instanceof DeleteProductPicturesTask);

        $companyDao->getById($company->getId());
    }
)
    ->throws(TDBMException::class);
