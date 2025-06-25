<?php

declare(strict_types=1);

namespace CompanyOS\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624154736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if table already exists
        $tableExists = $this->connection->executeQuery(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'stored_events'"
        )->fetchOne();

        if ($tableExists == 0) {
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql(<<<'SQL'
                CREATE TABLE stored_events (event_id CHAR(36) NOT NULL, aggregate_id CHAR(36) NOT NULL, event_name VARCHAR(255) NOT NULL, event_version VARCHAR(10) NOT NULL, event_data JSON NOT NULL, occurred_on DATETIME NOT NULL, PRIMARY KEY(event_id))
            SQL);
            $this->addSql(<<<'SQL'
                CREATE INDEX IDX_6B1BDE05D0BBCCBE ON stored_events (aggregate_id)
            SQL);
            $this->addSql(<<<'SQL'
                CREATE INDEX IDX_6B1BDE0541E832AD ON stored_events (event_name)
            SQL);
            $this->addSql(<<<'SQL'
                CREATE INDEX IDX_6B1BDE05E421E9EF ON stored_events (occurred_on)
            SQL);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE stored_events
        SQL);
    }
}
