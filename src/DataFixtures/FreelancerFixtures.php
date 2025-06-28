<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Freelancer Fixtures - For Solo Freelancer Use Case
 * Creates freelancer-specific roles, users, settings, and plugins
 */
class FreelancerFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createFreelancerRoles($manager);
        $this->createFreelancerUsers($manager);
        $this->createFreelancerSettings($manager);
        $this->createFreelancerPlugins($manager);
        $this->createFreelancerWebhooks($manager);
        
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['freelancer', 'all'];
    }

    public function getDependencies(): array
    {
        return [\CompanyOS\Bundle\CoreBundle\DataFixtures\CoreSystemFixtures::class];
    }

    private function createFreelancerRoles(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'freelancer_owner',
                'display_name' => 'Freelancer Owner',
                'description' => 'Hauptverantwortlicher Freelancer mit allen Rechten',
                'permissions' => [
                    'user.read', 'user.write', 'role.read',
                    'client.read', 'client.write', 'client.manage',
                    'project.read', 'project.write', 'project.manage',
                    'invoice.read', 'invoice.write', 'invoice.send',
                    'time.track', 'time.report', 'time.export',
                    'contract.read', 'contract.write', 'contract.sign',
                    'portfolio.read', 'portfolio.write', 'portfolio.publish',
                    'finance.read', 'finance.write', 'finance.report',
                    'tax.read', 'tax.write', 'tax.export'
                ]
            ],
            [
                'name' => 'freelancer_client',
                'display_name' => 'Freelancer Client',
                'description' => 'Kunde des Freelancers mit beschränkten Rechten',
                'permissions' => [
                    'project.read', 'project.comment',
                    'timeline.read', 'milestone.read',
                    'invoice.read', 'invoice.download',
                    'communication.read', 'communication.write',
                    'file.download', 'feedback.write'
                ]
            ],
            [
                'name' => 'freelancer_accountant',
                'display_name' => 'Freelancer Accountant',
                'description' => 'Steuerberater/Buchhalter mit Finanz-Zugriff',
                'permissions' => [
                    'invoice.read', 'invoice.export',
                    'time.read', 'time.export',
                    'finance.read', 'finance.export',
                    'tax.read', 'tax.write', 'tax.export',
                    'report.financial', 'report.tax'
                ]
            ],
            [
                'name' => 'freelancer_subcontractor',
                'display_name' => 'Subcontractor',
                'description' => 'Unterauftragnehmer mit limitierten Projektrechten',
                'permissions' => [
                    'project.read', 'project.contribute',
                    'time.track', 'time.read',
                    'file.upload', 'file.download',
                    'communication.read', 'communication.write'
                ]
            ]
        ];

        foreach ($roles as $role) {
            $manager->getConnection()->executeStatement("
                INSERT INTO roles (id, name, display_name, description, permissions, is_system, created_at, updated_at) VALUES
                (UUID(), ?, ?, ?, ?, 0, NOW(), NOW())
            ", [
                $role['name'],
                $role['display_name'],
                $role['description'],
                json_encode($role['permissions'])
            ]);
        }
    }

    private function createFreelancerUsers(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'info@maria-webdesign.de', 'first_name' => 'Maria', 'last_name' => 'Gonzalez', 'role' => 'freelancer_owner'],
            ['email' => 'kontakt@startup-innovativ.com', 'first_name' => 'Tech', 'last_name' => 'Startup', 'role' => 'freelancer_client'],
            ['email' => 'ceo@lokale-baeckerei.de', 'first_name' => 'Hans', 'last_name' => 'Müller', 'role' => 'freelancer_client'],
            ['email' => 'marketing@fitness-studio.com', 'first_name' => 'Sandra', 'last_name' => 'Weber', 'role' => 'freelancer_client'],
            ['email' => 'steuerberatung@kanzlei-schmidt.de', 'first_name' => 'Peter', 'last_name' => 'Schmidt', 'role' => 'freelancer_accountant'],
            ['email' => 'developer@freelance-partner.net', 'first_name' => 'Alex', 'last_name' => 'Thompson', 'role' => 'freelancer_subcontractor']
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
                password_hash('MariaDesign2024!', PASSWORD_BCRYPT)
            ]);

            // Assign role
            $roleId = $manager->getConnection()->fetchOne("SELECT id FROM roles WHERE name = ?", [$userData['role']]);
            $manager->getConnection()->executeStatement("
                INSERT INTO user_roles (id, user_id, role_id, assigned_at) VALUES
                (UUID(), ?, ?, NOW())
            ", [$userId, $roleId]);
        }
    }

    private function createFreelancerSettings(ObjectManager $manager): void
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
                UUID(), 'Maria Gonzalez Webdesign', 'Maria Gonzalez Webdesign', 'DE234567890', 'DE123987654',
                'Kreativweg', '15', '22083', 'Hamburg', 'Deutschland', 'Hamburg',
                'info@maria-webdesign.de', '+49 40 98765432', NULL, 'https://www.maria-webdesign.de', 'support@maria-webdesign.de',
                'de', 'EUR', 'Europe/Berlin',
                'd.m.Y', 'H:i', '1.234,56',
                'Maria Design Studio', 'https://cdn.maria-webdesign.de/logo.png', 'freelancer_client',
                3600, 0,
                'Maria Gonzalez', 'noreply@maria-webdesign.de', 'info@maria-webdesign.de',
                'smtp.strato.de', 587, 'tls', 'info@maria-webdesign.de', 'smtp_password_freelancer',
                ?, NOW(), NOW()
            )
        ", [
            json_encode([
                'formal_client' => 'Liebe/r {firstName} {lastName}',
                'informal_client' => 'Hallo {firstName}',
                'project_start' => 'Herzlich willkommen bei unserem gemeinsamen Projekt, {firstName}!',
                'invoice' => 'Sehr geehrte Damen und Herren',
                'friendly' => 'Hi {firstName}! 😊',
                'professional' => 'Sehr geehrte/r {title} {lastName}'
            ])
        ]);
    }

    private function createFreelancerPlugins(ObjectManager $manager): void
    {
        $plugins = [
            [
                'name' => 'invoice-generator-pro',
                'version' => '3.2.1',
                'author' => 'FreelancerTools',
                'active' => true,
                'meta' => [
                    'category' => 'finance',
                    'description' => 'Professionelle Rechnungserstellung mit deutscher Rechtssicherheit',
                    'features' => ['rechnung_erstellen', 'umsatzsteuer', 'kleinunternehmer', 'mahnung', 'export_datev'],
                    'license' => 'premium',
                    'price' => 29.99,
                    'compliance' => ['GoBD', 'UStG', 'AO']
                ]
            ],
            [
                'name' => 'time-tracking-simple',
                'version' => '2.5.0',
                'author' => 'ProductivityApps',
                'active' => true,
                'meta' => [
                    'category' => 'productivity',
                    'description' => 'Einfache Zeiterfassung für Freelancer-Projekte',
                    'features' => ['project_timer', 'manual_entry', 'reporting', 'export_csv', 'client_reports'],
                    'license' => 'freemium',
                    'free_hours' => 40
                ]
            ],
            [
                'name' => 'client-portal-lite',
                'version' => '1.8.0',
                'author' => 'ClientCommunication',
                'active' => true,
                'meta' => [
                    'category' => 'communication',
                    'description' => 'Kundenportal für Projektfortschritt und Dateiaustausch',
                    'features' => ['file_sharing', 'progress_tracking', 'feedback_system', 'notifications'],
                    'license' => 'premium',
                    'price' => 19.99
                ]
            ],
            [
                'name' => 'tax-assistant-germany',
                'version' => '1.4.0',
                'author' => 'TaxSoftware',
                'active' => true,
                'meta' => [
                    'category' => 'finance',
                    'description' => 'Steuerliche Unterstützung für deutsche Freelancer',
                    'features' => ['ust_voranmeldung', 'euer_berechnung', 'betriebsausgaben', 'steuer_export'],
                    'license' => 'premium',
                    'price' => 39.99,
                    'tax_year' => 2024
                ]
            ],
            [
                'name' => 'contract-templates-de',
                'version' => '1.2.0',
                'author' => 'LegalTemplates',
                'active' => true,
                'meta' => [
                    'category' => 'legal',
                    'description' => 'Deutsche Vertragsvorlagen für Freelancer',
                    'features' => ['werkvertrag', 'dienstvertrag', 'agb', 'datenschutz', 'widerruf'],
                    'license' => 'premium',
                    'price' => 49.99,
                    'legal_compliance' => ['DSGVO', 'BGB']
                ]
            ]
        ];

        foreach ($plugins as $plugin) {
            $manager->getConnection()->executeStatement("
                INSERT INTO plugins (id, name, version, author, active, meta, created_at, updated_at) VALUES
                (UUID(), ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $plugin['name'],
                $plugin['version'],
                $plugin['author'],
                $plugin['active'] ? 1 : 0,
                json_encode($plugin['meta'])
            ]);
        }
    }

    private function createFreelancerWebhooks(ObjectManager $manager): void
    {
        $webhooks = [
            [
                'name' => 'Banking Integration',
                'url' => 'https://api.banking-app.de/webhooks/transactions',
                'events' => ['invoice.paid', 'payment.received'],
                'is_active' => true,
                'secret' => 'banking_webhook_secret_123'
            ],
            [
                'name' => 'Tax Software Sync',
                'url' => 'https://steuer-software.de/api/webhooks/income',
                'events' => ['invoice.sent', 'payment.received', 'expense.added'],
                'is_active' => true,
                'secret' => 'tax_software_secret_456'
            ],
            [
                'name' => 'Client Notifications',
                'url' => 'https://notification-service.com/api/webhooks/freelancer',
                'events' => ['project.milestone', 'file.uploaded', 'invoice.sent'],
                'is_active' => true,
                'secret' => 'client_notification_secret_789'
            ],
            [
                'name' => 'Calendar Integration',
                'url' => 'https://calendar-api.com/webhooks/appointments',
                'events' => ['project.deadline', 'meeting.scheduled'],
                'is_active' => true,
                'secret' => 'calendar_integration_secret_101'
            ]
        ];

        foreach ($webhooks as $webhook) {
            $manager->getConnection()->executeStatement("
                INSERT INTO webhooks (id, name, url, events, is_active, secret, created_at, updated_at) VALUES
                (UUID(), ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
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
} 