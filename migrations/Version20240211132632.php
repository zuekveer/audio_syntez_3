<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240211132632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users
                    (
                guid uuid NOT NULL PRIMARY KEY DEFAULT uuid_generate_v4(),
                role VARCHAR not null,
                name VARCHAR not null,
                password VARCHAR not null,
                email VARCHAR not null,
                phone VARCHAR NOT NULL ,
                verified BOOLEAN default false,
                token VARCHAR,
                created_at TIMESTAMP not null default current_timestamp, 
                updated_at TIMESTAMP not null default current_timestamp
            )'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
