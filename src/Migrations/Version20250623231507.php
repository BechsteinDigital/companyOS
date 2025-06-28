<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Duplicate Migration - Skip
 * Tables were already created in Version20250101000000
 */
final class Version20250623231507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Skip duplicate migration - tables already exist in Version20250101000000';
    }

    public function up(Schema $schema): void
    {
        // Skip this migration - tables already created in Version20250101000000
        // This migration does nothing to avoid conflicts
    }

    public function down(Schema $schema): void
    {
        // Skip this migration - tables already created in Version20250101000000
        // This migration does nothing to avoid conflicts
    }
}
