<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240227120959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE wallet (
                id UUID NOT NULL PRIMARY KEY, 
                user_id UUID NOT NULL, UNIQUE (user_id), FOREIGN KEY (user_id) REFERENCES users(guid) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                balance INT DEFAULT 0, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE wallet');
    }
}
