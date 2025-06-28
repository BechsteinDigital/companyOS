<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * NeuroAI Fixtures - For AI Agency Supporting Neurodivergent Entrepreneurs
 * Creates fixtures for AI agency specializing in neurodivergent support with n8n automation
 */
class NeuroAIFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createNeuroAIRoles($manager);
        $this->createNeuroAIUsers($manager);
        $this->createNeuroAISettings($manager);
        $this->createNeuroAIPlugins($manager);
        $this->createNeuroAIWebhooks($manager);
        
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['neuroai', 'all'];
    }

    public function getDependencies(): array
    {
        return [\CompanyOS\Bundle\CoreBundle\DataFixtures\CoreSystemFixtures::class];
    }

    private function createNeuroAIRoles(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'ai_director',
                'display_name' => 'AI Director',
                'description' => 'KI-Strategieleitung und Produktentwicklung',
                'permissions' => [
                    'user.read', 'user.write', 'role.read',
                    'ai.models.read', 'ai.models.write', 'ai.models.deploy',
                    'workflow.read', 'workflow.write', 'workflow.manage',
                    'n8n.read', 'n8n.write', 'n8n.admin',
                    'client.read', 'client.write', 'client.neuroassess',
                    'accessibility.read', 'accessibility.configure',
                    'training.read', 'training.write', 'training.conduct'
                ]
            ],
            [
                'name' => 'neuro_coach',
                'display_name' => 'Neurodivergenz Coach',
                'description' => 'Spezialist für neurodivergente Bedürfnisse',
                'permissions' => [
                    'client.read', 'client.write', 'client.neuroassess',
                    'accessibility.read', 'accessibility.write',
                    'support.neurodivergent', 'coaching.individual',
                    'accommodations.read', 'accommodations.write',
                    'tools.assistive.read', 'tools.assistive.configure',
                    'communication.adapted', 'progress.tracking'
                ]
            ],
            [
                'name' => 'ai_engineer',
                'display_name' => 'AI Engineer',
                'description' => 'KI-Entwicklung und -Implementation',
                'permissions' => [
                    'ai.models.read', 'ai.models.write', 'ai.models.train',
                    'api.ai.read', 'api.ai.write', 'api.ai.integrate',
                    'workflow.read', 'workflow.write', 'workflow.debug',
                    'n8n.read', 'n8n.write', 'n8n.develop',
                    'data.read', 'data.process', 'integration.read'
                ]
            ],
            [
                'name' => 'automation_specialist',
                'display_name' => 'Automation Specialist',
                'description' => 'n8n Workflow-Automatisierung',
                'permissions' => [
                    'workflow.read', 'workflow.write', 'workflow.manage',
                    'n8n.read', 'n8n.write', 'n8n.admin',
                    'automation.design', 'automation.implement',
                    'integration.read', 'integration.write',
                    'triggers.read', 'triggers.write'
                ]
            ],
            [
                'name' => 'neurodivergent_entrepreneur',
                'display_name' => 'Neurodivergent Entrepreneur',
                'description' => 'Neurodivergente Unternehmer (Klienten)',
                'permissions' => [
                    'profile.read', 'profile.write', 'profile.neuroconfig',
                    'accommodations.personal', 'tools.assistive.use',
                    'workflow.personal.read', 'workflow.personal.use',
                    'ai.assistant.use', 'automation.personal.use',
                    'support.access', 'resources.access'
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

    private function createNeuroAIUsers(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'dr.jensen@neuro-ai-lab.de', 'first_name' => 'Dr. Alex', 'last_name' => 'Jensen', 'role' => 'ai_director'],
            ['email' => 'maya.coach@neuro-ai-lab.de', 'first_name' => 'Maya', 'last_name' => 'NeuroCoach', 'role' => 'neuro_coach'],
            ['email' => 'kai.tech@neuro-ai-lab.de', 'first_name' => 'Kai', 'last_name' => 'AIEngineer', 'role' => 'ai_engineer'],
            ['email' => 'sam.automation@neuro-ai-lab.de', 'first_name' => 'Sam', 'last_name' => 'WorkflowExpert', 'role' => 'automation_specialist'],
            
            // Neurodivergente Klienten
            ['email' => 'adhd.freelancer@kreativ-chaos.de', 'first_name' => 'Leo', 'last_name' => 'ADHDCreative', 'role' => 'neurodivergent_entrepreneur'],
            ['email' => 'autistic.developer@logic-systems.de', 'first_name' => 'Aria', 'last_name' => 'AutisticDev', 'role' => 'neurodivergent_entrepreneur'],
            ['email' => 'dyslexic.designer@visual-thinking.de', 'first_name' => 'River', 'last_name' => 'DyslexicDesigner', 'role' => 'neurodivergent_entrepreneur']
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
                password_hash('NeuroAI2024!', PASSWORD_BCRYPT)
            ]);

            $roleId = $manager->getConnection()->fetchOne("SELECT id FROM roles WHERE name = ?", [$userData['role']]);
            $manager->getConnection()->executeStatement("
                INSERT INTO user_roles (id, user_id, role_id, assigned_at) VALUES
                (UUID(), ?, ?, NOW())
            ", [$userId, $roleId]);
        }
    }

    private function createNeuroAISettings(ObjectManager $manager): void
    {
        $manager->getConnection()->executeStatement("
            INSERT INTO company_settings (
                id, company_name, legal_name, tax_number, vat_number,
                street, house_number, postal_code, city, country,
                email, phone, website, support_email,
                default_language, default_currency, timezone,
                date_format, time_format, number_format,
                system_name, default_user_role, session_timeout, maintenance_mode,
                email_from_name, email_from_address, email_reply_to,
                smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password,
                salutations, created_at, updated_at
            ) VALUES (
                UUID(), 'NeuroAI Lab', 'NeuroAI Lab GmbH', 'DE456789012', 'DE567890234',
                'Inklusionsstraße', '42', '10247', 'Berlin', 'Deutschland',
                'info@neuro-ai-lab.de', '+49 30 98765432', 'https://www.neuro-ai-lab.de', 'support@neuro-ai-lab.de',
                'de', 'EUR', 'Europe/Berlin',
                'd.m.Y', 'H:i', '1.234,56',
                'NeuroAI Lab Dashboard', 'neurodivergent_entrepreneur', 5400, 0,
                'NeuroAI Lab Team', 'noreply@neuro-ai-lab.de', 'support@neuro-ai-lab.de',
                'smtp.neuro-ai-lab.de', 587, 'tls', 'noreply@neuro-ai-lab.de', 'smtp_password',
                ?, NOW(), NOW()
            )
        ", [
            json_encode([
                'neurodivergent_friendly' => 'Hallo {firstName}! 🌈',
                'clear_simple' => 'Hi {firstName}',
                'visual_friendly' => '👋 Hallo {firstName}!',
                'adhd_attention' => '🔥 WICHTIG: Hallo {firstName}!',
                'autism_direct' => 'Direkte Nachricht für {firstName}',
                'anxiety_supportive' => 'Du schaffst das, {firstName}! 💪'
            ])
        ]);
    }

    private function createNeuroAIPlugins(ObjectManager $manager): void
    {
        $plugins = [
            [
                'name' => 'n8n-workflow-engine',
                'version' => '1.2.0',
                'author' => 'NeuroAI Lab',
                'active' => true,
                'meta' => [
                    'category' => 'automation',
                    'description' => 'n8n Workflow-Engine für neurodivergente Automatisierung',
                    'features' => ['visual_workflows', 'drag_drop', 'neuro_friendly_ui'],
                    'license' => 'opensource',
                    'accessibility' => ['screen_reader', 'keyboard_navigation', 'high_contrast']
                ]
            ],
            [
                'name' => 'ai-assistant-neurodivergent',
                'version' => '2.1.0',
                'author' => 'NeuroAI Lab',
                'active' => true,
                'meta' => [
                    'category' => 'ai_assistant',
                    'description' => 'KI-Assistent für neurodivergente Bedürfnisse',
                    'features' => ['adhd_reminders', 'autism_routine_support', 'anxiety_management'],
                    'ai_models' => ['gpt-4', 'claude-3'],
                    'license' => 'premium',
                    'price' => 49.99
                ]
            ],
            [
                'name' => 'focus-time-manager',
                'version' => '1.5.0',
                'author' => 'ADHDTools',
                'active' => true,
                'meta' => [
                    'category' => 'productivity',
                    'description' => 'Zeitmanagement für ADHD',
                    'features' => ['pomodoro_adapted', 'flexible_breaks', 'hyperfocus_protection'],
                    'license' => 'premium',
                    'price' => 19.99
                ]
            ],
            [
                'name' => 'sensory-ui-adapter',
                'version' => '1.3.0',
                'author' => 'AccessibilityFirst',
                'active' => true,
                'meta' => [
                    'category' => 'accessibility',
                    'description' => 'UI-Anpassungen für sensorische Bedürfnisse',
                    'features' => ['reduced_motion', 'calm_colors', 'font_dyslexia'],
                    'license' => 'opensource'
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

    private function createNeuroAIWebhooks(ObjectManager $manager): void
    {
        $webhooks = [
            [
                'name' => 'n8n Workflow Trigger Hub',
                'url' => 'https://n8n.neuro-ai-lab.de/webhook/client-automation',
                'events' => ['client.onboarded', 'accommodation.needed', 'routine.changed'],
                'is_active' => true,
                'secret' => 'n8n_neuro_automation_secret_123'
            ],
            [
                'name' => 'AI Model Training Pipeline',
                'url' => 'https://ai-training.neuro-ai-lab.de/webhook/model-updates',
                'events' => ['data.anonymized', 'model.retrained', 'effectiveness.measured'],
                'is_active' => true,
                'secret' => 'ai_training_pipeline_secret_456'
            ],
            [
                'name' => 'Crisis Support System',
                'url' => 'https://crisis-support.neuro-ai-lab.de/api/webhooks/emergency',
                'events' => ['meltdown.detected', 'anxiety.spike', 'support.requested'],
                'is_active' => true,
                'secret' => 'crisis_support_secret_789'
            ],
            [
                'name' => 'Accessibility Monitor',
                'url' => 'https://accessibility.neuro-ai-lab.de/webhook/compliance',
                'events' => ['ui.accessibility_issue', 'accommodation.updated'],
                'is_active' => true,
                'secret' => 'accessibility_monitor_secret_101'
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