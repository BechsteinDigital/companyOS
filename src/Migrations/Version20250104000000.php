<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Plugin System Migration
 * Creates: plugins table
 */
final class Version20250104000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create plugin system table';
    }

    public function up(Schema $schema): void
    {
        // Plugins Table
        $this->addSql('CREATE TABLE plugins (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(255) NOT NULL,
            version VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            active TINYINT(1) NOT NULL,
            meta JSON DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE INDEX UNIQ_EC85F6715E237E06 (name)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE plugins');
    }
} 