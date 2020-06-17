<?php

declare(strict_types=1);

namespace App\Tests\UseCase;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UseCaseTestCase extends WebTestCase
{
    protected Connection $dbal;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->dbal = self::$container->get(Connection::class);
        $this->dbal->beginTransaction();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->dbal->rollBack();
        parent::tearDown();
    }
}
