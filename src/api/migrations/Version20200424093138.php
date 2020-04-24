<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use TheCodingMachine\FluidSchema\TdbmFluidSchema;
use RuntimeException;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200424093138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create rights, roles, roles_rights, users, companies, users_companies and products tables.';
    }

    public function up(Schema $schema) : void
    {
        $db = new TdbmFluidSchema($schema);

        $db->table('rights')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('code')->string(255)->notNull()->unique()->graphqlField();

        $db->table('roles')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('code')->string(255)->notNull()->unique()->graphqlField();

        $db->junctionTable('roles', 'rights')->graphqlField();

        $db->table('users')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('role_id')->references('roles')->notNull()->graphqlField()
            ->column('first_name')->string(255)->notNull()->graphqlField()
            ->column('last_name')->string(255)->notNull()->graphqlField()
            ->column('email')->string(255)->notNull()->unique()->graphqlField()
            ->column('password')->string(255)->null()->default(null);

        $db->table('companies')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('website')->string(255)->notNull()->unique()->graphqlField()
            ->column('logo_filename')->string(255)->null()->default(null)->graphqlField();

        $db->junctionTable('users', 'companies')->graphqlField();

        $db->table('products')
            ->column('id')->guid()->primaryKey()->comment('@UUID')->graphqlField()
            ->column('company_id')->references('companies')->notNull()->graphqlField()
            ->column('name')->string(255)->notNull()->unique()->graphqlField()
            ->column('price')->float()->null()->default(null)->graphqlField()
            ->column('margin')->float()->null()->default(null)->graphqlField()
            ->column('picture_filename')->string(255)->null()->default(null)->graphqlField();
    }

    public function down(Schema $schema) : void
    {
        throw new RuntimeException('Never rollback a migration!');
    }
}
