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
final class Version20200619151658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create companies and products tables.';
    }

    public function up(Schema $schema): void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('companies')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('user_id')->references('users')->notNull()->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('website')->string(255)->null()->default(null)->graphqlField();

        $db->table('products')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('company_id')->references('companies')->notNull()->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('price')->float()->notNull()->graphqlField()
            ->column('pictures')->array()->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema): void
    {
        throw new RuntimeException('Never rollback a migration!');
    }
}
