<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use RuntimeException;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200910075836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders table.';
    }

    public function up(Schema $schema): void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('orders')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('user_id')->references('users')->notNull()->graphqlField()
            ->column('product_id')->references('products')->notNull()->graphqlField()
            ->column('quantity')->integer()->notNull()->graphqlField()
            ->column('unit_price')->float()->notNull()->graphqlField()
            ->column('invoice')->string(255)->null()->default(null);
    }

    public function down(Schema $schema): void
    {
        throw new RuntimeException('Never rollback a migration!');
    }
}
