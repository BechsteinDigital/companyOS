<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for Hybrid Access Control System
 * Creates tables for: RBAC (Roles), ACL (Access Control Lists), ABAC (Attribute-based Rules), User-Role Mapping
 */
final class Version20250126160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Hybrid Access Control System tables (RBAC + ACL + ABAC)';
    }

    public function up(Schema $schema): void
    {
        // 1. Roles Table (RBAC)
        $this->addSql('CREATE TABLE roles (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(100) NOT NULL,
            display_name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            is_system TINYINT(1) NOT NULL DEFAULT 0,
            permissions JSON NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 2. User-Role Mapping Table
        $this->addSql('CREATE TABLE user_roles (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            role_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            assigned_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
            expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            assignment_reason TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE INDEX user_role_unique (user_id, role_id),
            INDEX idx_user_roles_user (user_id),
            INDEX idx_user_roles_role (role_id),
            CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 3. Access Control Entries Table (ACL)
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

        // 4. ABAC Rules Table (Attribute-Based Access Control)
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

        // Insert Standard Roles
        $this->addSql("INSERT INTO roles (id, name, display_name, description, is_system, permissions, created_at, updated_at) VALUES
            (UUID(), 'ROLE_ADMIN', 'Administrator', 'Full system access', 1, JSON_ARRAY('*'), NOW(), NOW()),
            (UUID(), 'ROLE_MANAGER', 'Manager', 'Management access', 1, JSON_ARRAY('user.manage', 'report.view', 'document.manage'), NOW(), NOW()),
            (UUID(), 'ROLE_EMPLOYEE', 'Employee', 'Standard employee access', 1, JSON_ARRAY('document.read', 'document.write', 'profile.edit'), NOW(), NOW()),
            (UUID(), 'ROLE_USER', 'User', 'Basic user access', 1, JSON_ARRAY('profile.view', 'document.read'), NOW(), NOW())
        ");

        // Insert Standard ABAC Rules
        $this->addSql("INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, created_at, updated_at) VALUES
            (UUID(), 'Working Hours Rule', 'user.delete', 'Prevent user deletion outside working hours', JSON_OBJECT('time', JSON_OBJECT('\$between', JSON_ARRAY('09:00', '17:00'))), 'deny', 100, 1, NOW(), NOW()),
            (UUID(), 'Department Rule', 'document.edit', 'Allow document editing only within same department', JSON_OBJECT('user.department', JSON_OBJECT('\$eq', 'document.department')), 'allow', 50, 1, NOW(), NOW()),
            (UUID(), 'Admin Override', '*', 'Admins bypass all ABAC rules', JSON_OBJECT('user.role', JSON_OBJECT('\$in', JSON_ARRAY('ROLE_ADMIN'))), 'allow', 1000, 1, NOW(), NOW())
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE abac_rules');
        $this->addSql('DROP TABLE access_control_entries');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE roles');
    }
} 