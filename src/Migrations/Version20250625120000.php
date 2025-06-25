<?php

declare(strict_types=1);

namespace CompanyOS\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250625120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Erstellt die Tabelle webhooks für das Webhook-System';
    }

    public function up(Schema $schema): void
    {
        // Check if table already exists
        $tableExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'webhooks'"
        )->fetchOne();

        if ($tableExists == 0) {
            $this->addSql('CREATE TABLE webhooks (
                id CHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                url TEXT NOT NULL,
                events JSON NOT NULL,
                is_active TINYINT(1) NOT NULL,
                secret VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE webhooks');
    }
} 