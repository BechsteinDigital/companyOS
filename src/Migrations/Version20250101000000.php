<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial Migration: Core User System
 * Creates: users, roles, user_roles tables
 */
final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create core user system tables (users, roles, user_roles)';
    }

    public function up(Schema $schema): void
    {
        // 1. Users Table
        $this->addSql('CREATE TABLE users (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            email VARCHAR(255) NOT NULL COMMENT \'(DC2Type:email)\',
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            password_hash VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 2. Roles Table  
        $this->addSql('CREATE TABLE roles (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            name VARCHAR(100) NOT NULL,
            display_name VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            permissions JSON NOT NULL,
            is_system TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 3. User-Roles Mapping Table
        $this->addSql('CREATE TABLE user_roles (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            role_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            assigned_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id),
            UNIQUE INDEX user_role_unique (user_id, role_id),
            INDEX IDX_54FCD59FA76ED395 (user_id),
            INDEX IDX_54FCD59FD60322AC (role_id),
            CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id),
            CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // 4. Insert Default Roles
        $this->addSql("INSERT INTO roles (id, name, display_name, description, permissions, is_system, created_at, updated_at) VALUES
            (UUID(), 'admin', 'Administrator', 'Vollzugriff auf alle Funktionen', JSON_ARRAY('user.read', 'user.write', 'role.read', 'role.write', 'plugin.read', 'plugin.write', 'settings.read', 'settings.write', 'webhook.read', 'webhook.write', 'client.read', 'client.write', 'profile.read', 'profile.write', 'auth.read', 'auth.write'), 1, NOW(), NOW()),
            (UUID(), 'manager', 'Manager', 'Verwaltung von Benutzern und Inhalten', JSON_ARRAY('user.read', 'user.write', 'plugin.read', 'plugin.write'), 0, NOW(), NOW()),
            (UUID(), 'user', 'Benutzer', 'Standard-Benutzer mit Basisrechten', JSON_ARRAY('profile.read', 'profile.write'), 0, NOW(), NOW())
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
    }
} 