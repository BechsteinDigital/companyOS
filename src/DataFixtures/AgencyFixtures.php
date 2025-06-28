<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Agency Fixtures - For Digital Agency Use Case
 * Creates agency-specific roles, users, settings, and plugins
 */
class AgencyFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createCompanySettings($manager);
        $this->createOAuth2Clients($manager);
        $this->createAgencyRoles($manager);
        $this->createAgencyUsers($manager);
        $this->createAbacRules($manager);
        $this->createAclEntries($manager);
        
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['agency', 'all'];
    }

    public function getDependencies(): array
    {
        return [\CompanyOS\Bundle\CoreBundle\DataFixtures\CoreSystemFixtures::class];
    }

    private function createAgencyRoles(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'creative_director',
                'display_name' => 'Creative Director',
                'description' => 'Leitung des kreativen Teams, strategische Entscheidungen',
                'permissions' => [
                    'user.read', 'user.write', 'role.read',
                    'project.read', 'project.write', 'project.manage',
                    'client.read', 'client.write', 'client.manage',
                    'design.read', 'design.write', 'design.approve',
                    'campaign.read', 'campaign.write', 'campaign.approve',
                    'budget.read', 'budget.write', 'budget.approve',
                    'report.read', 'report.write'
                ]
            ],
            [
                'name' => 'account_manager',
                'display_name' => 'Account Manager',
                'description' => 'Kundenbetreuung und Projektkoordination',
                'permissions' => [
                    'client.read', 'client.write', 'client.manage',
                    'project.read', 'project.write',
                    'timeline.read', 'timeline.write',
                    'invoice.read', 'invoice.write',
                    'report.read', 'communication.read', 'communication.write'
                ]
            ],
            [
                'name' => 'designer',
                'display_name' => 'Designer',
                'description' => 'Ausführung von Design-Arbeiten',
                'permissions' => [
                    'design.read', 'design.write',
                    'asset.read', 'asset.write',
                    'brand.read', 'project.read',
                    'timeline.read'
                ]
            ],
            [
                'name' => 'developer',
                'display_name' => 'Developer',
                'description' => 'Frontend- und Backend-Entwicklung',
                'permissions' => [
                    'code.read', 'code.write',
                    'asset.read', 'design.read',
                    'project.read', 'timeline.read',
                    'deployment.read'
                ]
            ],
            [
                'name' => 'project_manager',
                'display_name' => 'Project Manager',
                'description' => 'Projektplanung und -überwachung',
                'permissions' => [
                    'project.read', 'project.write', 'project.manage',
                    'timeline.read', 'timeline.write', 'timeline.manage',
                    'task.read', 'task.write', 'task.assign',
                    'report.read', 'report.write',
                    'budget.read', 'resource.read', 'resource.assign'
                ]
            ],
            [
                'name' => 'agency_client',
                'display_name' => 'Agency Client',
                'description' => 'Externe Kunden der Agentur',
                'permissions' => [
                    'project.read', 'timeline.read',
                    'design.read', 'design.feedback',
                    'report.read', 'communication.read', 'communication.write',
                    'invoice.read'
                ]
            ]
        ];

        foreach ($roles as $role) {
            $manager->getConnection()->executeStatement("
                INSERT INTO roles (id, name, display_name, description, permissions, is_system, created_at, updated_at) VALUES
                (?, ?, ?, ?, ?, 0, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $role['name'],
                $role['display_name'],
                $role['description'],
                json_encode($role['permissions'])
            ]);
        }
    }

    private function createAgencyUsers(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'sarah.mueller@pixelagentur.de', 'first_name' => 'Sarah', 'last_name' => 'Müller', 'role' => 'creative_director'],
            ['email' => 'thomas.weber@pixelagentur.de', 'first_name' => 'Thomas', 'last_name' => 'Weber', 'role' => 'account_manager'],
            ['email' => 'lisa.schmidt@pixelagentur.de', 'first_name' => 'Lisa', 'last_name' => 'Schmidt', 'role' => 'designer'],
            ['email' => 'max.krueger@pixelagentur.de', 'first_name' => 'Max', 'last_name' => 'Krüger', 'role' => 'designer'],
            ['email' => 'anna.bauer@pixelagentur.de', 'first_name' => 'Anna', 'last_name' => 'Bauer', 'role' => 'developer'],
            ['email' => 'david.hoffmann@pixelagentur.de', 'first_name' => 'David', 'last_name' => 'Hoffmann', 'role' => 'developer'],
            ['email' => 'julia.richter@pixelagentur.de', 'first_name' => 'Julia', 'last_name' => 'Richter', 'role' => 'project_manager'],
            ['email' => 'kunde@tech-startup.com', 'first_name' => 'Michael', 'last_name' => 'Johnson', 'role' => 'agency_client'],
        ];

        foreach ($users as $userData) {
            $userId = $this->generateUuid();
            $manager->getConnection()->executeStatement("
                INSERT INTO users (id, email, first_name, last_name, password_hash, is_active, created_at, updated_at) VALUES
                (?, ?, ?, ?, ?, 1, NOW(), NOW())
            ", [
                $userId,
                $userData['email'],
                $userData['first_name'],
                $userData['last_name'],
                password_hash('PixelAgentur2024!', PASSWORD_BCRYPT)
            ]);

            // Assign role
            $roleId = $manager->getConnection()->fetchOne("SELECT id FROM roles WHERE name = ?", [$userData['role']]);
            $manager->getConnection()->executeStatement("
                INSERT INTO user_roles (id, user_id, role_id, assigned_at) VALUES
                (?, ?, ?, NOW())
            ", [$this->generateUuid(), $userId, $roleId]);
        }
    }

    private function createAgencySettings(ObjectManager $manager): void
    {
        $manager->getConnection()->executeStatement("
            INSERT INTO company_settings (
                id, company_name, legal_name, tax_number, vat_number,
                street, house_number, postal_code, city, country, state,
                email, phone, fax, website, support_email,
                default_language, default_currency, timezone,
                date_format, time_format, number_format,
                system_name, logo_url, default_user_role,
                session_timeout, maintenance_mode,
                email_from_name, email_from_address, email_reply_to,
                smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password,
                salutations, created_at, updated_at
            ) VALUES (
                ?, 'PixelAgentur', 'PixelAgentur GmbH', 'DE123456789', 'DE987654321',
                'Kreativstraße', '42', '10115', 'Berlin', 'Deutschland', 'Berlin',
                'info@pixelagentur.de', '+49 30 12345678', '+49 30 12345679', 'https://www.pixelagentur.de', 'support@pixelagentur.de',
                'de', 'EUR', 'Europe/Berlin',
                'd.m.Y', 'H:i', '1.234,56',
                'PixelAgentur Dashboard', 'https://cdn.pixelagentur.de/logo.png', 'designer',
                7200, 0,
                'PixelAgentur Team', 'noreply@pixelagentur.de', 'reply@pixelagentur.de',
                'smtp.pixelagentur.de', 587, 'tls', 'smtp@pixelagentur.de', 'smtp_password_here',
                ?, NOW(), NOW()
            )
        ", [
            $this->generateUuid(),
            json_encode([
                'formal_male' => 'Sehr geehrter Herr {lastName}',
                'formal_female' => 'Sehr geehrte Frau {lastName}',
                'informal' => 'Hallo {firstName}',
                'creative' => 'Hi {firstName}! 🎨',
                'client_formal' => 'Liebe/r {firstName} {lastName}',
                'team_internal' => 'Hey {firstName}! 👋'
            ])
        ]);
    }

    private function createAgencyPlugins(ObjectManager $manager): void
    {
        $plugins = [
            [
                'name' => 'adobe-creative-suite',
                'version' => '1.2.0',
                'author' => 'PixelAgentur',
                'active' => true,
                'meta' => [
                    'category' => 'design',
                    'description' => 'Integration mit Adobe Creative Suite für Asset-Management',
                    'features' => ['asset_sync', 'version_control', 'collaboration'],
                    'license' => 'premium',
                    'price' => 99.99
                ]
            ],
            [
                'name' => 'figma-integration',
                'version' => '2.1.0',
                'author' => 'PixelAgentur',
                'active' => true,
                'meta' => [
                    'category' => 'design',
                    'description' => 'Figma-Integration für kollaboratives Design',
                    'features' => ['real_time_sync', 'comment_system', 'handoff'],
                    'license' => 'free'
                ]
            ],
            [
                'name' => 'project-time-tracking',
                'version' => '1.5.0',
                'author' => 'PixelAgentur',
                'active' => true,
                'meta' => [
                    'category' => 'project_management',
                    'description' => 'Zeiterfassung für Projekte und Kunden',
                    'features' => ['time_tracking', 'invoicing', 'reporting'],
                    'license' => 'premium',
                    'price' => 49.99
                ]
            ],
            [
                'name' => 'client-feedback-system',
                'version' => '1.0.0',
                'author' => 'PixelAgentur',
                'active' => true,
                'meta' => [
                    'category' => 'communication',
                    'description' => 'Strukturiertes Feedback-System für Kunden',
                    'features' => ['feedback_forms', 'approval_workflow', 'notifications'],
                    'license' => 'premium',
                    'price' => 29.99
                ]
            ]
        ];

        foreach ($plugins as $plugin) {
            $manager->getConnection()->executeStatement("
                INSERT INTO plugins (id, name, version, author, active, meta, created_at, updated_at) VALUES
                (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $plugin['name'],
                $plugin['version'],
                $plugin['author'],
                $plugin['active'] ? 1 : 0,
                json_encode($plugin['meta'])
            ]);
        }
    }

    private function createAgencyWebhooks(ObjectManager $manager): void
    {
        $webhooks = [
            [
                'name' => 'Slack Project Notifications',
                'url' => 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX',
                'events' => ['project.created', 'project.completed', 'deadline.approaching'],
                'is_active' => true,
                'secret' => 'slack_webhook_secret_123'
            ],
            [
                'name' => 'Client Portal Updates',
                'url' => 'https://client-portal.pixelagentur.de/api/webhooks/updates',
                'events' => ['design.uploaded', 'feedback.requested', 'project.milestone'],
                'is_active' => true,
                'secret' => 'client_portal_secret_456'
            ],
            [
                'name' => 'Invoice System Integration',
                'url' => 'https://invoicing.pixelagentur.de/api/webhooks/time-tracking',
                'events' => ['timesheet.submitted', 'project.completed'],
                'is_active' => true,
                'secret' => 'invoice_system_secret_789'
            ]
        ];

        foreach ($webhooks as $webhook) {
            $manager->getConnection()->executeStatement("
                INSERT INTO webhooks (id, name, url, events, is_active, secret, created_at, updated_at) VALUES
                (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $webhook['name'],
                $webhook['url'],
                json_encode($webhook['events']),
                $webhook['is_active'] ? 1 : 0,
                $webhook['secret']
            ]);
        }
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

    /**
     * Create ABAC Rules for agency context
     */
    private function createAbacRules(ObjectManager $manager): void
    {
        // Dashboard Access - Extended hours for agency work
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'agency_dashboard_hours', 'dashboard.view', 'Dashboard-Zugriff für Agentur-Arbeitszeiten', ?, 'allow', 100, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'time' => [
                    'hours' => ['$between' => [6, 23]], // Extended hours 6:00 - 23:00
                    'weekdays' => ['$in' => [1, 2, 3, 4, 5, 6, 7]] // All days
                ]
            ]),
            json_encode(['created_by' => 'agency_system', 'type' => 'time_restriction'])
        ]);

        // Client Management - Account Managers and Project Managers only
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'client_management_roles', 'client.create', 'Klient-Verwaltung nur für Account und Project Manager', ?, 'allow', 200, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'roles' => ['$in' => ['account_manager', 'project_manager', 'agency_admin']]
                ]
            ]),
            json_encode(['created_by' => 'agency_system', 'type' => 'role_restriction'])
        ]);

        // Project Management - Project Managers and above
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'project_management_access', 'project.create', 'Projekt-Verwaltung für Manager-Rollen', ?, 'allow', 300, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'roles' => ['$in' => ['project_manager', 'account_manager', 'agency_admin']]
                ]
            ]),
            json_encode(['created_by' => 'agency_system', 'type' => 'role_restriction'])
        ]);

        // Financial Access - Business hours only
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'financial_business_hours', 'finance.read', 'Finanz-Zugriff nur während Geschäftszeiten', ?, 'allow', 400, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                '$and' => [
                    [
                        'time' => [
                            'hours' => ['$between' => [9, 18]], // Business hours
                            'weekdays' => ['$in' => [1, 2, 3, 4, 5]] // Weekdays only
                        ]
                    ],
                    [
                        'user' => [
                            'roles' => ['$in' => ['agency_admin', 'financial_controller']]
                        ]
                    ]
                ]
            ]),
            json_encode(['created_by' => 'agency_system', 'type' => 'financial_restriction'])
        ]);

        // User Management - Admin and HR only
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'user_management_admin_hr', 'user.create', 'Benutzerverwaltung nur für Admin und HR', ?, 'allow', 500, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'roles' => ['$in' => ['agency_admin', 'hr_manager']]
                ]
            ]),
            json_encode(['created_by' => 'agency_system', 'type' => 'admin_hr_restriction'])
        ]);
    }

    /**
     * Create ACL Entries for agency users
     */
    private function createAclEntries(ObjectManager $manager): void
    {
        // Get user IDs from database - using correct email addresses
        $creativeDirectorId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'sarah.mueller@pixelagentur.de'");
        $accountManagerId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'thomas.weber@pixelagentur.de'");
        $projectManagerId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'julia.richter@pixelagentur.de'");

        // Dashboard Access for Creative Director (admin role)
        if ($creativeDirectorId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'agency-dashboard', 'dashboard', 'dashboard.view', 'allow', ?, 'Creative Director has full dashboard access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $creativeDirectorId,
                $creativeDirectorId
            ]);
        }

        // Client Management Access for Account Manager
        if ($accountManagerId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'client-management', 'clients', 'client.create', 'allow', ?, 'Account manager needs client management access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $accountManagerId,
                $creativeDirectorId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'client-management', 'clients', 'client.read', 'allow', ?, 'Account manager needs client management access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $accountManagerId,
                $creativeDirectorId
            ]);
        }

        // Project Management Access for Project Manager
        if ($projectManagerId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'project-management', 'projects', 'project.create', 'allow', ?, 'Project manager needs project access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $projectManagerId,
                $creativeDirectorId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'project-management', 'projects', 'project.read', 'allow', ?, 'Project manager needs project access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $projectManagerId,
                $creativeDirectorId
            ]);
        }

        // User Management for Creative Director
        if ($creativeDirectorId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'user-management', 'users', 'user.create', 'allow', ?, 'Creative Director needs user management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $creativeDirectorId,
                $creativeDirectorId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'user-management', 'users', 'user.read', 'allow', ?, 'Creative Director needs user management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $creativeDirectorId,
                $creativeDirectorId
            ]);

            // Role Management for Creative Director
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'role-management', 'roles', 'role.create', 'allow', ?, 'Creative Director needs role management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $creativeDirectorId,
                $creativeDirectorId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'role-management', 'roles', 'role.read', 'allow', ?, 'Creative Director needs role management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $creativeDirectorId,
                $creativeDirectorId
            ]);
        }
    }
} 