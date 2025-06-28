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
        $this->createAbacRules($manager);
        $this->createAclEntries($manager);
        
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

    /**
     * Create ABAC Rules for context-based access control
     */
    private function createAbacRules(ObjectManager $manager): void
    {
        // Dashboard Access Rule - Basic working hours
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'dashboard_working_hours', 'dashboard.view', 'Dashboard nur während Arbeitszeiten', ?, 'allow', 100, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'time' => [
                    'hours' => ['$between' => [6, 22]], // 6:00 - 22:00
                    'weekdays' => ['$in' => [1, 2, 3, 4, 5, 6, 7]] // Alle Wochentage
                ]
            ]),
            json_encode(['created_by' => 'system', 'type' => 'time_restriction'])
        ]);

        // User Management - Restricted to IT Department
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'user_management_it_only', 'user.create', 'Benutzerverwaltung nur für IT-Abteilung', ?, 'allow', 200, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'department' => ['$in' => ['IT', 'Administration', 'Management']]
                ]
            ]),
            json_encode(['created_by' => 'system', 'type' => 'department_restriction'])
        ]);

        // Plugin Management - High Security
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'plugin_management_secure', 'plugin.install', 'Plugin-Installation nur aus sicheren Netzwerken', ?, 'allow', 300, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                '$and' => [
                    [
                        'user' => [
                            'department' => ['$in' => ['IT']]
                        ]
                    ],
                    [
                        'environment' => [
                            'ip_address' => ['$regex' => '/^(192\.168\.|10\.0\.|172\.16\.)/']
                        ]
                    ]
                ]
            ]),
            json_encode(['created_by' => 'system', 'type' => 'ip_department_restriction'])
        ]);

        // System Settings - Critical Access
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'system_settings_critical', 'settings.system', 'System-Einstellungen nur für autorisierte Administratoren', ?, 'allow', 400, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                '$and' => [
                    [
                        'user' => [
                            'roles' => ['$in' => ['super_admin', 'system_admin']]
                        ]
                    ],
                    [
                        'time' => [
                            'hours' => ['$between' => [9, 17]], // Nur Geschäftszeiten
                            'weekdays' => ['$in' => [1, 2, 3, 4, 5]] // Werktage
                        ]
                    ]
                ]
            ]),
            json_encode(['created_by' => 'system', 'type' => 'critical_system_access'])
        ]);

        // Role Management - Highest Security
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'role_management_highest_security', 'role.create', 'Rollenverwaltung mit höchsten Sicherheitsanforderungen', ?, 'allow', 500, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                '$and' => [
                    [
                        'user' => [
                            'department' => ['$in' => ['IT', 'Security']]
                        ]
                    ],
                    [
                        'time' => [
                            'hours' => ['$between' => [9, 16]], // Verkürzte Arbeitszeit
                            'weekdays' => ['$in' => [1, 2, 3, 4, 5]]
                        ]
                    ],
                    [
                        'environment' => [
                            'ip_address' => ['$regex' => '/^192\.168\.10\./'] // Nur Admin-Netzwerk
                        ]
                    ]
                ]
            ]),
            json_encode(['created_by' => 'system', 'type' => 'highest_security_access'])
        ]);
    }

    /**
     * Create ACL Entries for specific resource permissions
     */
    private function createAclEntries(ObjectManager $manager): void
    {
        // Get admin user ID from database
        $adminUserId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'admin@companyos.dev'");

        if (!$adminUserId) {
            return; // Skip if admin user doesn't exist
        }

        // Dashboard Resource Access for Admin
        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'system-dashboard', 'dashboard', 'dashboard.view', 'allow', ?, 'System admin has unrestricted dashboard access', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        // System Settings Access for Admin
        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'system-settings', 'settings', 'settings.system', 'allow', ?, 'System admin requires full settings access', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        // User Management Access for Admin
        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'user-management', 'users', 'user.create', 'allow', ?, 'Admin needs user management capabilities', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'user-management', 'users', 'user.read', 'allow', ?, 'Admin needs user management capabilities', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'user-management', 'users', 'user.update', 'allow', ?, 'Admin needs user management capabilities', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'user-management', 'users', 'user.delete', 'allow', ?, 'Admin needs user management capabilities', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        // Plugin Management Access for Admin
        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'plugin-management', 'plugins', 'plugin.install', 'allow', ?, 'Admin needs plugin management access', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        // Role Management Access for Admin
        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'role-management', 'roles', 'role.create', 'allow', ?, 'Admin needs role management access', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);

        $manager->getConnection()->executeStatement("
            INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
            (?, ?, 'role-management', 'roles', 'role.read', 'allow', ?, 'Admin needs role management access', NULL, NOW(), NOW())
        ", [
            $this->generateUuid(),
            $adminUserId,
            $adminUserId
        ]);
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