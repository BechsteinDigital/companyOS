<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Core System Fixtures - Essential for all use cases
 * Creates OAuth2 clients, system roles, and admin user
 */
class CoreSystemFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createOAuth2Clients($manager);
        $this->createSystemRoles($manager);
        $this->createSystemAdmin($manager);
        
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['core', 'all'];
    }

    private function createOAuth2Clients(ObjectManager $manager): void
    {
        // Backend API Client
        $manager->getConnection()->executeStatement("
            INSERT INTO oauth2_client (identifier, name, secret, redirect_uris, grants, scopes, active, allow_plain_text_pkce) VALUES
            ('backend-api', 'Backend API Client', NULL, NULL, ?, ?, 1, 0)
        ", [
            json_encode(['password', 'refresh_token']),
            json_encode([
                'user.read', 'user.write', 'role.read', 'role.write',
                'plugin.read', 'plugin.write', 'settings.read', 'settings.write',
                'webhook.read', 'webhook.write', 'client.read', 'client.write',
                'profile.read', 'profile.write', 'auth.read', 'auth.write'
            ])
        ]);

        // Frontend SPA Client
        $manager->getConnection()->executeStatement("
            INSERT INTO oauth2_client (identifier, name, secret, redirect_uris, grants, scopes, active, allow_plain_text_pkce) VALUES
            ('frontend-spa', 'Frontend SPA Client', NULL, ?, ?, ?, 1, 1)
        ", [
            json_encode(['http://localhost:3000/callback', 'http://localhost:8080/callback']),
            json_encode(['authorization_code', 'refresh_token']),
            json_encode([
                'user.read', 'profile.read', 'profile.write',
                'plugin.read', 'settings.read'
            ])
        ]);

        // Mobile App Client
        $manager->getConnection()->executeStatement("
            INSERT INTO oauth2_client (identifier, name, secret, redirect_uris, grants, scopes, active, allow_plain_text_pkce) VALUES
            ('mobile-app', 'Mobile App Client', NULL, ?, ?, ?, 1, 1)
        ", [
            json_encode(['companyos://oauth/callback']),
            json_encode(['authorization_code', 'refresh_token']),
            json_encode([
                'user.read', 'profile.read', 'profile.write',
                'plugin.read', 'settings.read'
            ])
        ]);
    }

    private function createSystemRoles(ObjectManager $manager): void
    {
        // Super Admin Role
        $manager->getConnection()->executeStatement("
            INSERT INTO roles (id, name, display_name, description, permissions, is_system, created_at, updated_at) VALUES
            (?, 'super_admin', 'Super Administrator', 'Vollzugriff auf alle Funktionen inklusive Systemeinstellungen', ?, 1, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode(['*']) // All permissions
        ]);

        // System User Role
        $manager->getConnection()->executeStatement("
            INSERT INTO roles (id, name, display_name, description, permissions, is_system, created_at, updated_at) VALUES
            (?, 'user', 'Standard User', 'Grundlegende Benutzerrechte', ?, 1, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode(['profile.read', 'profile.write'])
        ]);
    }

    private function createSystemAdmin(ObjectManager $manager): void
    {
        // Create system admin user
        $adminId = $this->generateUuid();
        $manager->getConnection()->executeStatement("
            INSERT INTO users (id, email, first_name, last_name, password_hash, is_active, created_at, updated_at) VALUES
            (?, 'admin@companyos.dev', 'System', 'Administrator', ?, 1, NOW(), NOW())
        ", [
            $adminId,
            password_hash('CompanyOS2024!', PASSWORD_BCRYPT)
        ]);

        // Assign super_admin role
        $superAdminRoleId = $manager->getConnection()->fetchOne("SELECT id FROM roles WHERE name = 'super_admin'");
                    $manager->getConnection()->executeStatement("
                INSERT INTO user_roles (id, user_id, role_id, assigned_at) VALUES
                (?, ?, ?, NOW())
            ", [$this->generateUuid(), $adminId, $superAdminRoleId]);
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
} 