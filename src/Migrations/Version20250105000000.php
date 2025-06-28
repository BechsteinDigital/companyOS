<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Webhook System Migration
 * Creates: webhooks table
 */
final class Version20250105000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create webhook system table';
    }

    public function up(Schema $schema): void
    {
        // Webhooks Table
        $this->addSql('CREATE TABLE webhooks (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(255) NOT NULL,
            url LONGTEXT NOT NULL,
            events JSON NOT NULL,
            is_active TINYINT(1) NOT NULL,
            secret VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE webhooks');
    }
} 