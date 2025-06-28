<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Duplicate Migration - Skip
 * Table was already created in Version20250103000000
 */
final class Version20250625130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Skip duplicate migration - company_settings table already exists in Version20250103000000';
    }

    public function up(Schema $schema): void
    {
        // Skip this migration - table already created in Version20250103000000
        // This migration does nothing to avoid conflicts
    }

    public function down(Schema $schema): void
    {
        // Skip this migration - table already created in Version20250103000000
        // This migration does nothing to avoid conflicts
    }
} 