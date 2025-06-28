<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Ecommerce Fixtures - For Online Shop Use Case
 * Creates ecommerce-specific roles, users, settings, and plugins
 */
class EcommerceFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createEcommerceRoles($manager);
        $this->createEcommerceUsers($manager);
        $this->createEcommerceSettings($manager);
        $this->createEcommercePlugins($manager);
        $this->createEcommerceWebhooks($manager);
        $this->createAbacRules($manager);
        $this->createAclEntries($manager);
        
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['ecommerce', 'all'];
    }

    public function getDependencies(): array
    {
        return [\CompanyOS\Bundle\CoreBundle\DataFixtures\CoreSystemFixtures::class];
    }

    private function createEcommerceRoles(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'shop_owner',
                'display_name' => 'Shop Owner',
                'description' => 'Shopinhaber mit vollständigen Rechten',
                'permissions' => [
                    'user.read', 'user.write', 'role.read', 'role.write',
                    'product.read', 'product.write', 'product.manage',
                    'category.read', 'category.write', 'category.manage',
                    'order.read', 'order.write', 'order.manage',
                    'customer.read', 'customer.write', 'customer.manage',
                    'inventory.read', 'inventory.write', 'inventory.manage',
                    'payment.read', 'payment.write', 'payment.configure',
                    'shipping.read', 'shipping.write', 'shipping.configure',
                    'promotion.read', 'promotion.write', 'promotion.manage',
                    'analytics.read', 'analytics.export',
                    'tax.read', 'tax.write', 'tax.configure',
                    'seo.read', 'seo.write', 'content.manage'
                ]
            ],
            [
                'name' => 'shop_manager',
                'display_name' => 'Shop Manager',
                'description' => 'Shop-Manager für tägliche Operationen',
                'permissions' => [
                    'product.read', 'product.write',
                    'category.read', 'category.write',
                    'order.read', 'order.write', 'order.process',
                    'customer.read', 'customer.support',
                    'inventory.read', 'inventory.update',
                    'promotion.read', 'promotion.write',
                    'analytics.read', 'content.write'
                ]
            ],
            [
                'name' => 'product_manager',
                'display_name' => 'Product Manager',
                'description' => 'Produktverantwortlicher für Katalog-Management',
                'permissions' => [
                    'product.read', 'product.write', 'product.manage',
                    'category.read', 'category.write',
                    'inventory.read', 'inventory.write',
                    'supplier.read', 'supplier.write',
                    'pricing.read', 'pricing.write',
                    'seo.read', 'seo.write'
                ]
            ],
            [
                'name' => 'customer_service',
                'display_name' => 'Customer Service',
                'description' => 'Kundenservice-Mitarbeiter',
                'permissions' => [
                    'customer.read', 'customer.support',
                    'order.read', 'order.process', 'order.status',
                    'return.read', 'return.process',
                    'communication.read', 'communication.write',
                    'refund.process', 'coupon.create'
                ]
            ],
            [
                'name' => 'warehouse_manager',
                'display_name' => 'Warehouse Manager',
                'description' => 'Lagerverantwortlicher',
                'permissions' => [
                    'inventory.read', 'inventory.write', 'inventory.manage',
                    'order.read', 'order.fulfill', 'order.ship',
                    'supplier.read', 'supplier.orders',
                    'warehouse.read', 'warehouse.manage',
                    'shipping.read', 'shipping.process'
                ]
            ],
            [
                'name' => 'marketing_manager',
                'display_name' => 'Marketing Manager',
                'description' => 'Marketing- und Promotion-Verantwortlicher',
                'permissions' => [
                    'promotion.read', 'promotion.write', 'promotion.manage',
                    'coupon.read', 'coupon.write', 'coupon.manage',
                    'newsletter.read', 'newsletter.write', 'newsletter.send',
                    'analytics.read', 'analytics.marketing',
                    'seo.read', 'seo.write', 'content.manage',
                    'social.read', 'social.write'
                ]
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Buchhalter für Finanz-Management',
                'permissions' => [
                    'order.read', 'order.financial',
                    'payment.read', 'payment.reconcile',
                    'tax.read', 'tax.calculate', 'tax.report',
                    'invoice.read', 'invoice.export',
                    'analytics.financial', 'report.financial'
                ]
            ],
            [
                'name' => 'shop_customer',
                'display_name' => 'Shop Customer',
                'description' => 'Registrierte Kunden des Online-Shops',
                'permissions' => [
                    'profile.read', 'profile.write',
                    'order.read', 'order.personal',
                    'wishlist.read', 'wishlist.write',
                    'review.read', 'review.write',
                    'address.read', 'address.write',
                    'payment.personal', 'newsletter.subscribe'
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

    private function createEcommerceUsers(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'owner@fashionstyle-shop.de', 'first_name' => 'Lisa', 'last_name' => 'Fashionista', 'role' => 'shop_owner'],
            ['email' => 'manager@fashionstyle-shop.de', 'first_name' => 'Michael', 'last_name' => 'Commerce', 'role' => 'shop_manager'],
            ['email' => 'products@fashionstyle-shop.de', 'first_name' => 'Anna', 'last_name' => 'Katalog', 'role' => 'product_manager'],
            ['email' => 'service@fashionstyle-shop.de', 'first_name' => 'Tom', 'last_name' => 'Support', 'role' => 'customer_service'],
            ['email' => 'lager@fashionstyle-shop.de', 'first_name' => 'Klaus', 'last_name' => 'Lagerfeld', 'role' => 'warehouse_manager'],
            ['email' => 'marketing@fashionstyle-shop.de', 'first_name' => 'Sarah', 'last_name' => 'Promotion', 'role' => 'marketing_manager'],
            ['email' => 'buchhaltung@fashionstyle-shop.de', 'first_name' => 'Peter', 'last_name' => 'Zahlen', 'role' => 'accountant'],
            ['email' => 'kunde1@gmail.com', 'first_name' => 'Emma', 'last_name' => 'Mustermann', 'role' => 'shop_customer'],
            ['email' => 'kunde2@yahoo.de', 'first_name' => 'Max', 'last_name' => 'Beispiel', 'role' => 'shop_customer'],
            ['email' => 'vip.kunde@premium.de', 'first_name' => 'Victoria', 'last_name' => 'VIP', 'role' => 'shop_customer']
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
                password_hash('FashionStyle2024!', PASSWORD_BCRYPT)
            ]);

            // Assign role
            $roleId = $manager->getConnection()->fetchOne("SELECT id FROM roles WHERE name = ?", [$userData['role']]);
            $manager->getConnection()->executeStatement("
                INSERT INTO user_roles (id, user_id, role_id, assigned_at) VALUES
                (?, ?, ?, NOW())
            ", [$this->generateUuid(), $userId, $roleId]);
        }
    }

    private function createEcommerceSettings(ObjectManager $manager): void
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
                ?, 'FashionStyle Shop', 'FashionStyle GmbH & Co. KG', 'DE345678901', 'DE456789123',
                'Modestraße', '88', '80331', 'München', 'Deutschland', 'Bayern',
                'info@fashionstyle-shop.de', '+49 89 12345678', '+49 89 12345679', 'https://www.fashionstyle-shop.de', 'support@fashionstyle-shop.de',
                'de', 'EUR', 'Europe/Berlin',
                'd.m.Y', 'H:i', '1.234,56',
                'FashionStyle Admin Panel', 'https://cdn.fashionstyle-shop.de/logo.png', 'shop_customer',
                1800, 0,
                'FashionStyle Team', 'noreply@fashionstyle-shop.de', 'service@fashionstyle-shop.de',
                'smtp.fashionstyle-shop.de', 587, 'tls', 'noreply@fashionstyle-shop.de', 'smtp_fashion_password',
                ?, NOW(), NOW()
            )
        ", [
            $this->generateUuid(),
            json_encode([
                'welcome_customer' => 'Herzlich willkommen bei FashionStyle, {firstName}!',
                'order_confirmation' => 'Liebe/r {firstName} {lastName}',
                'shipping_notification' => 'Hallo {firstName}',
                'newsletter_formal' => 'Sehr geehrte Damen und Herren',
                'newsletter_personal' => 'Liebe/r {firstName}',
                'support_ticket' => 'Hallo {firstName}, vielen Dank für Ihre Nachricht!',
                'vip_customer' => 'Liebe/r {title} {lastName}',
                'birthday_greeting' => 'Alles Gute zum Geburtstag, {firstName}! 🎉'
            ])
        ]);
    }

    private function createEcommercePlugins(ObjectManager $manager): void
    {
        $plugins = [
            [
                'name' => 'paypal-payment-pro',
                'version' => '4.2.1',
                'author' => 'EcommerceTools',
                'active' => true,
                'meta' => [
                    'category' => 'payment',
                    'description' => 'PayPal Zahlungsintegration mit Express Checkout',
                    'features' => ['paypal_express', 'paypal_plus', 'credit_card', 'installments', 'buyer_protection'],
                    'license' => 'free',
                    'supported_currencies' => ['EUR', 'USD', 'GBP'],
                    'compliance' => ['PCI-DSS', 'GDPR']
                ]
            ],
            [
                'name' => 'stripe-payments-complete',
                'version' => '3.8.2',
                'author' => 'PaymentExperts',
                'active' => true,
                'meta' => [
                    'category' => 'payment',
                    'description' => 'Stripe Komplett-Integration mit allen Zahlungsmethoden',
                    'features' => ['credit_cards', 'sepa', 'giropay', 'sofort', 'klarna', 'apple_pay', 'google_pay'],
                    'license' => 'premium',
                    'price' => 79.99,
                    'compliance' => ['PCI-DSS', 'GDPR', 'PSD2']
                ]
            ],
            [
                'name' => 'dhl-shipping-integration',
                'version' => '2.5.0',
                'author' => 'LogisticsPartner',
                'active' => true,
                'meta' => [
                    'category' => 'shipping',
                    'description' => 'DHL Versandintegration mit Paketschein-Erstellung',
                    'features' => ['label_printing', 'tracking', 'pickup_service', 'international_shipping'],
                    'license' => 'premium',
                    'price' => 49.99,
                    'countries' => ['DE', 'AT', 'CH', 'NL', 'BE']
                ]
            ],
            [
                'name' => 'google-analytics-enhanced',
                'version' => '1.9.0',
                'author' => 'AnalyticsTools',
                'active' => true,
                'meta' => [
                    'category' => 'analytics',
                    'description' => 'Google Analytics 4 mit Enhanced Ecommerce Tracking',
                    'features' => ['ecommerce_tracking', 'conversion_goals', 'custom_dimensions', 'gtm_integration'],
                    'license' => 'free',
                    'privacy_compliant' => true
                ]
            ],
            [
                'name' => 'seo-optimizer-pro',
                'version' => '2.1.5',
                'author' => 'SEOExperts',
                'active' => true,
                'meta' => [
                    'category' => 'seo',
                    'description' => 'Professionelle SEO-Optimierung für Online-Shops',
                    'features' => ['meta_tags', 'structured_data', 'xml_sitemap', 'breadcrumbs', 'canonical_urls'],
                    'license' => 'premium',
                    'price' => 59.99
                ]
            ],
            [
                'name' => 'newsletter-mailchimp',
                'version' => '3.4.0',
                'author' => 'MarketingTools',
                'active' => true,
                'meta' => [
                    'category' => 'marketing',
                    'description' => 'Mailchimp Newsletter-Integration mit Automation',
                    'features' => ['subscriber_sync', 'automation', 'segmentation', 'abandoned_cart', 'product_recommendations'],
                    'license' => 'freemium',
                    'free_contacts' => 2000
                ]
            ],
            [
                'name' => 'product-reviews-advanced',
                'version' => '1.6.2',
                'author' => 'CustomerEngagement',
                'active' => true,
                'meta' => [
                    'category' => 'customer_engagement',
                    'description' => 'Erweiterte Produktbewertungen mit Foto-Upload',
                    'features' => ['photo_reviews', 'video_reviews', 'q_and_a', 'review_incentives', 'moderation'],
                    'license' => 'premium',
                    'price' => 39.99
                ]
            ],
            [
                'name' => 'inventory-management-smart',
                'version' => '2.8.1',
                'author' => 'WarehouseTools',
                'active' => true,
                'meta' => [
                    'category' => 'inventory',
                    'description' => 'Intelligente Lagerverwaltung mit Prognosen',
                    'features' => ['stock_forecasting', 'low_stock_alerts', 'supplier_integration', 'batch_tracking'],
                    'license' => 'premium',
                    'price' => 89.99
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

    private function createEcommerceWebhooks(ObjectManager $manager): void
    {
        $webhooks = [
            [
                'name' => 'Order Processing Automation',
                'url' => 'https://erp-system.fashionstyle.de/api/webhooks/orders',
                'events' => ['order.created', 'order.paid', 'order.shipped', 'order.completed'],
                'is_active' => true,
                'secret' => 'erp_webhook_secret_fashion123'
            ],
            [
                'name' => 'Inventory Sync Warehouse',
                'url' => 'https://warehouse-management.de/api/webhooks/inventory',
                'events' => ['product.stock_low', 'product.out_of_stock', 'order.fulfilled'],
                'is_active' => true,
                'secret' => 'warehouse_sync_secret_456'
            ],
            [
                'name' => 'Marketing Automation',
                'url' => 'https://marketing-platform.com/api/webhooks/fashion',
                'events' => ['customer.registered', 'order.abandoned_cart', 'customer.birthday'],
                'is_active' => true,
                'secret' => 'marketing_automation_secret_789'
            ],
            [
                'name' => 'Accounting Software Sync',
                'url' => 'https://buchhaltung-software.de/api/webhooks/revenue',
                'events' => ['order.invoiced', 'payment.received', 'refund.processed'],
                'is_active' => true,
                'secret' => 'accounting_sync_secret_101'
            ],
            [
                'name' => 'Customer Support Integration',
                'url' => 'https://support-desk.fashionstyle.de/api/webhooks/tickets',
                'events' => ['order.problem', 'return.requested', 'customer.complaint'],
                'is_active' => true,
                'secret' => 'support_integration_secret_202'
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
     * Create ABAC Rules for E-commerce context
     */
    private function createAbacRules(ObjectManager $manager): void
    {
        // E-commerce dashboard - Extended business hours
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'ecommerce_extended_hours', 'dashboard.view', 'E-Commerce Dashboard für erweiterte Geschäftszeiten', ?, 'allow', 100, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'time' => [
                    'hours' => ['$between' => [7, 22]], // 7:00 - 22:00
                    'weekdays' => ['$in' => [1, 2, 3, 4, 5, 6, 7]] // All days
                ]
            ]),
            json_encode(['created_by' => 'ecommerce_system', 'type' => 'extended_business_hours'])
        ]);

        // Order Management - Sales and fulfillment teams
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'order_management_teams', 'order.manage', 'Bestellverwaltung für Verkaufs- und Fulfillment-Teams', ?, 'allow', 200, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'roles' => ['$in' => ['sales_manager', 'fulfillment_manager', 'store_manager', 'customer_service']]
                ]
            ]),
            json_encode(['created_by' => 'ecommerce_system', 'type' => 'order_management'])
        ]);

        // Financial Access - Critical restrictions
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'financial_critical_access', 'finance.read', 'Kritischer Finanz-Zugriff mit Einschränkungen', ?, 'allow', 300, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                '$and' => [
                    [
                        'user' => [
                            'roles' => ['$in' => ['store_manager', 'financial_analyst']]
                        ]
                    ],
                    [
                        'time' => [
                            'hours' => ['$between' => [9, 17]], // Business hours only
                            'weekdays' => ['$in' => [1, 2, 3, 4, 5]] // Weekdays only
                        ]
                    ],
                    [
                        'environment' => [
                            'secure_location' => ['$eq' => true]
                        ]
                    ]
                ]
            ]),
            json_encode(['created_by' => 'ecommerce_system', 'type' => 'financial_security'])
        ]);

        // Inventory Management - Warehouse access
        $manager->getConnection()->executeStatement("
            INSERT INTO abac_rules (id, name, permission, description, conditions, effect, priority, is_active, metadata, created_at, updated_at) VALUES
            (?, 'inventory_warehouse_access', 'inventory.manage', 'Lagerverwaltung für Warehouse-Teams', ?, 'allow', 400, 1, ?, NOW(), NOW())
        ", [
            $this->generateUuid(),
            json_encode([
                'user' => [
                    'roles' => ['$in' => ['fulfillment_manager', 'warehouse_supervisor', 'inventory_specialist']]
                ],
                'location' => [
                    'department' => ['$in' => ['Warehouse', 'Fulfillment', 'Logistics']]
                ]
            ]),
            json_encode(['created_by' => 'ecommerce_system', 'type' => 'warehouse_operations'])
        ]);
    }

    /**
     * Create ACL Entries for E-commerce users
     */
    private function createAclEntries(ObjectManager $manager): void
    {
        // Get user IDs for E-commerce team
        $storeManagerId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'emily.store@fashionhub.com'");
        $salesManagerId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'michael.sales@fashionhub.com'");
        $fulfillmentManagerId = $manager->getConnection()->fetchOne("SELECT id FROM users WHERE email = 'sarah.fulfillment@fashionhub.com'");

        // Store Manager - Full access
        if ($storeManagerId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'ecommerce-dashboard', 'dashboard', 'dashboard.view', 'allow', ?, 'Store Manager needs full dashboard access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $storeManagerId,
                $storeManagerId
            ]);

            // User Management for Store Manager
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'user-management', 'users', 'user.create', 'allow', ?, 'Store Manager needs user management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $storeManagerId,
                $storeManagerId
            ]);
        }

        // Sales Manager - Order and customer access
        if ($salesManagerId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'order-management', 'orders', 'order.manage', 'allow', ?, 'Sales Manager needs order management', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $salesManagerId,
                $storeManagerId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'customer-management', 'customers', 'customer.read', 'allow', ?, 'Sales Manager needs customer access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $salesManagerId,
                $storeManagerId
            ]);
        }

        // Fulfillment Manager - Inventory and shipping
        if ($fulfillmentManagerId) {
            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'inventory-management', 'inventory', 'inventory.manage', 'allow', ?, 'Fulfillment Manager needs inventory access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $fulfillmentManagerId,
                $storeManagerId
            ]);

            $manager->getConnection()->executeStatement("
                INSERT INTO access_control_entries (id, user_id, resource_id, resource_type, permission, type, granted_by, reason, expires_at, created_at, updated_at) VALUES
                (?, ?, 'shipping-management', 'shipping', 'shipping.manage', 'allow', ?, 'Fulfillment Manager needs shipping access', NULL, NOW(), NOW())
            ", [
                $this->generateUuid(),
                $fulfillmentManagerId,
                $storeManagerId
            ]);
        }
    }
} 