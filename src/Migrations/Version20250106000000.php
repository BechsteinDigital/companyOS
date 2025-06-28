<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Event System Migration
 * Creates: stored_events table (Event Sourcing)
 */
final class Version20250106000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event system table (stored_events)';
    }

    public function up(Schema $schema): void
    {
        // Stored Events Table (Event Sourcing)
        $this->addSql('CREATE TABLE stored_events (
            event_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            aggregate_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            event_name VARCHAR(255) NOT NULL,
            event_version VARCHAR(10) NOT NULL,
            event_data JSON NOT NULL,
            occurred_on DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(event_id),
            INDEX IDX_6B1BDE05D0BBCCBE (aggregate_id),
            INDEX IDX_6B1BDE0541E832AD (event_name),
            INDEX IDX_6B1BDE05E421E9EF (occurred_on)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE stored_events');
    }
} 