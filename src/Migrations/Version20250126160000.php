<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for Hybrid Access Control System - Part 1
 * Adds missing ACL and ABAC tables, extends existing user_roles table
 */
final class Version20250126160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing ACL and ABAC tables, extend user_roles for Hybrid Access Control';
    }

    public function up(Schema $schema): void
    {
        // Skip this migration - duplicated by Version20250128210000
        // This migration does nothing to avoid conflicts
    }

    public function down(Schema $schema): void
    {
        // Skip this migration - duplicated by Version20250128210000
        // This migration does nothing to avoid conflicts
    }
} 