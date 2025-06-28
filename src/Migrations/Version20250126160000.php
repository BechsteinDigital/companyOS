<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for Hybrid Access Control System - Part 2
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
        // 1. Extend existing user_roles table with new fields
        $this->addSql('ALTER TABLE user_roles 
            ADD COLUMN assigned_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
            ADD COLUMN expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            ADD COLUMN assignment_reason TEXT DEFAULT NULL,
            ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\',
            CHANGE assigned_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        // 2. Update existing roles table structure (add missing fields if needed)
        $this->addSql('ALTER TABLE roles 
            ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        // 3. Access Control Entries Table (ACL) - NEW
        $this->addSql('CREATE TABLE access_control_entries (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            resource_id VARCHAR(255) NOT NULL,
            resource_type VARCHAR(100) NOT NULL,
            permission VARCHAR(100) NOT NULL,
            type VARCHAR(20) NOT NULL,
            granted_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
            reason TEXT DEFAULT NULL,
            expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            INDEX idx_acl_user_resource (user_id, resource_id, resource_type),
            INDEX idx_acl_resource (resource_id, resource_type),
            CONSTRAINT FK_ACL_USER FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 4. ABAC Rules Table (Attribute-Based Access Control) - NEW
        $this->addSql('CREATE TABLE abac_rules (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(255) NOT NULL,
            permission VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            conditions JSON NOT NULL,
            effect VARCHAR(20) NOT NULL,
            priority INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            metadata JSON DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            INDEX idx_abac_permission (permission),
            INDEX idx_abac_priority (priority)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 5. Insert Standard ABAC Rules
        $this->addSql("INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, created_at, updated_at) VALUES
            (UUID(), 'Working Hours Rule', 'user.delete', 'Prevent user deletion outside working hours', JSON_OBJECT('time', JSON_OBJECT('\$between', JSON_ARRAY('09:00', '17:00'))), 'deny', 100, 1, NOW(), NOW()),
            (UUID(), 'Department Rule', 'document.edit', 'Allow document editing only within same department', JSON_OBJECT('user.department', JSON_OBJECT('\$eq', 'document.department')), 'allow', 50, 1, NOW(), NOW()),
            (UUID(), 'Admin Override', '*', 'Admins bypass all ABAC rules', JSON_OBJECT('user.role', JSON_OBJECT('\$in', JSON_ARRAY('admin'))), 'allow', 1000, 1, NOW(), NOW())
        ");

        // 6. Add constraints to roles table if not exists  
        $this->addSql('ALTER TABLE roles ADD UNIQUE INDEX IF NOT EXISTS UNIQ_B63E2EC75E237E06 (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE abac_rules');
        $this->addSql('DROP TABLE access_control_entries');
        
        // Revert user_roles table changes
        $this->addSql('ALTER TABLE user_roles 
            DROP COLUMN assigned_by,
            DROP COLUMN expires_at, 
            DROP COLUMN assignment_reason,
            DROP COLUMN updated_at,
            CHANGE created_at assigned_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');
    }
} 