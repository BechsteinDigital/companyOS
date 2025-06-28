<?php

namespace CompanyOS\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Main App Fixtures - Entry Point for Demo Data
 * 
 * This fixture provides information about available fixture groups for different use cases.
 * To load specific fixtures, use the --group parameter:
 * 
 * Core System (Required):
 * php bin/console doctrine:fixtures:load --group=core
 * 
 * Use Case Specific:
 * php bin/console doctrine:fixtures:load --group=agency      # Digital Agency
 * php bin/console doctrine:fixtures:load --group=freelancer # Solo Freelancer  
 * php bin/console doctrine:fixtures:load --group=ecommerce  # Online Shop
 * 
 * Load Everything:
 * php bin/console doctrine:fixtures:load --group=all
 * 
 * Or load multiple groups:
 * php bin/console doctrine:fixtures:load --group=core --group=agency
 */
class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // This fixture intentionally does nothing.
        // Use the specific fixture groups instead.
        
        echo "\n";
        echo "🎯 CompanyOS Demo Fixtures Available:\n";
        echo "=====================================\n\n";
        
        echo "📋 Core System (Required):\n";
        echo "   php bin/console doctrine:fixtures:load --group=core\n";
        echo "   → OAuth2 Clients, System Roles, Admin User\n\n";
        
        echo "🏢 Digital Agency Use Case:\n";
        echo "   php bin/console doctrine:fixtures:load --group=agency\n";
        echo "   → Creative Director, Designers, Developers, Clients\n";
        echo "   → PixelAgentur Demo Company\n\n";
        
        echo "👨‍💻 Freelancer Use Case:\n";
        echo "   php bin/console doctrine:fixtures:load --group=freelancer\n";
        echo "   → Solo Freelancer, Clients, Accountant, Subcontractor\n";
        echo "   → Maria Gonzalez Webdesign Demo\n\n";
        
        echo "🛒 Ecommerce Use Case:\n";
        echo "   php bin/console doctrine:fixtures:load --group=ecommerce\n";
        echo "   → Shop Owner, Product Manager, Customer Service, Customers\n";
        echo "   → FashionStyle Shop Demo\n\n";
        
        echo "🧠 NeuroAI Use Case:\n";
        echo "   php bin/console doctrine:fixtures:load --group=neuroai\n";
        echo "   → AI Director, Neuro Coach, AI Engineer, Automation Specialist\n";
        echo "   → NeuroAI Lab - KI-Unterstützung für Neurodivergente mit n8n\n\n";
        
        echo "🚀 Load Everything:\n";
        echo "   php bin/console doctrine:fixtures:load --group=all\n\n";
        
        echo "💡 Pro Tip: Start with 'core' then add your preferred use case!\n";
        echo "\n";
    }

    public static function getGroups(): array
    {
        return ['info'];
    }
} 