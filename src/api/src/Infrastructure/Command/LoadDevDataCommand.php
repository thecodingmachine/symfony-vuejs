<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Throwable\Exists\CompanyWithNameExists;
use App\Domain\Throwable\Exists\ProductWithNameExists;
use App\Domain\Throwable\Invalid\InvalidCompany;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use App\Domain\Throwable\Invalid\InvalidProduct;
use App\Domain\Throwable\Invalid\InvalidProductPicture;
use App\UseCase\Company\CreateCompany;
use App\UseCase\Product\CreateProduct;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use function mt_rand;

final class LoadDevDataCommand extends Command
{
    private Connection $dbal;
    private KernelInterface $kernel;
    private CreateCompany $createCompany;
    private CreateProduct $createProduct;

    public function __construct(
        Connection $dbal,
        KernelInterface $kernel,
        CreateCompany $createCompany,
        CreateProduct $createProduct
    ) {
        $this->dbal          = $dbal;
        $this->kernel        = $kernel;
        $this->createCompany = $createCompany;
        $this->createProduct = $createProduct;

        parent::__construct('app:load-data:dev');
    }

    /**
     * @throws CompanyWithNameExists
     * @throws InvalidCompanyLogo
     * @throws InvalidCompany
     * @throws ProductWithNameExists
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->kernel->getEnvironment() !== 'dev') {
            $output->writeln(
                'Wrong environment: expected "dev" got "' .
                $this->kernel->getEnvironment() .
                '"'
            );

            return Command::FAILURE;
        }

        $this->purge();
        $this->load();

        return Command::SUCCESS;
    }

    private function purge(): void
    {
        // TODO purge uploaded files.
        $this->dbal->executeQuery('DELETE FROM products');
        $this->dbal->executeQuery('DELETE FROM companies');
    }

    /**
     * @throws CompanyWithNameExists
     * @throws InvalidCompanyLogo
     * @throws InvalidCompany
     * @throws ProductWithNameExists
     * @throws InvalidProductPicture
     * @throws InvalidProduct
     */
    private function load(): void
    {
        // TODO improve (uploaded files in storage etc.).
        $company = $this->createCompany->createCompany('Foo');

        for ($i = 0; $i < 20; $i++) {
            $this->createProduct->createProduct(
                'Product ' . $i,
                mt_rand(10, 1000),
                $company
            );
        }
    }
}
