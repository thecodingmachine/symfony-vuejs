<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Throwable\InvalidModel;
use App\Domain\Throwable\InvalidStorable;
use App\Infrastructure\Fixtures\AppFixtures;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class DevFixturesCommand extends Command
{
    private KernelInterface $kernel;
    private AppFixtures $fixtures;

    public function __construct(
        KernelInterface $kernel,
        AppFixtures $fixtures
    ) {
        $this->kernel   = $kernel;
        $this->fixtures = $fixtures;

        parent::__construct('app:fixtures:dev');
    }

    /**
     * @throws InvalidModel
     * @throws InvalidStorable
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

        $this->fixtures->purge();
        $this->fixtures->load();

        return Command::SUCCESS;
    }
}
