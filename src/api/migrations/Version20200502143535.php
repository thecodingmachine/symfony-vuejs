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
final class Version20200502143535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create reset_password_tokens table.';
    }

    public function up(Schema $schema): void
    {
        $db = new TdbmFluidSchema($schema);
        $db->table('reset_password_tokens')
            ->column('id')->guid()->primaryKey()->comment('@UUID')
            ->column('user_id')->references('users')->notNull()->unique()
            ->column('token')->string(255)->notNull()->unique()
            ->column('valid_until')->datetimeImmutable()->notNull();
    }

    public function down(Schema $schema): void
    {
        throw new RuntimeException('Never rollback a migration!');
    }
}
