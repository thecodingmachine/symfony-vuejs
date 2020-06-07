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
final class Version20200424093138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create sessions table.';
    }

    public function up(Schema $schema): void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('sessions')
            ->column('sess_id')->string(128)->notNull()->primaryKey()
            ->column('sess_data')->blob()->notNull()
            ->column('sess_time')->integer()->notNull()
            ->column('sess_lifetime')->integer()->notNull();
    }

    public function down(Schema $schema): void
    {
        throw new RuntimeException('Never rollback a migration!');
    }
}
