<?php

namespace CompanyOS;

use CompanyOS\DependencyInjection\CompanyOSCoreExtension;
use CompanyOS\Infrastructure\Plugin\DependencyInjection\PluginCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class CompanyOSCoreBundle extends Bundle
{
    public function getContainerExtension(): CompanyOSCoreExtension
    {
        return new CompanyOSCoreExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        // Plugin Compiler Pass registrieren
        $container->addCompilerPass(new PluginCompilerPass());
    }

    public function boot(): void
    {
        parent::boot();
        
        // Plugin Manager initialisieren
        if ($this->container->has('CompanyOS\Domain\Plugin\Domain\Service\PluginManager')) {
            $pluginManager = $this->container->get('CompanyOS\Domain\Plugin\Domain\Service\PluginManager');
            $pluginManager->loadPlugins();
        }
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadRoutes(RoutingConfigurator $routes): void
    {
        // User API
        $routes->import('../../src/Application/User/Controller/', 'attribute')
            ->prefix('/api/users');

        // Role API
        $routes->import('../../src/Application/Role/Controller/', 'attribute')
            ->prefix('/api/roles');

        // Plugin API
        $routes->import('../../src/Application/Plugin/Controller/', 'attribute')
            ->prefix('/api/plugins');

        // Webhook API
        $routes->import('../../src/Application/Webhook/Controller/', 'attribute')
            ->prefix('/api/webhooks');

        // Settings API
        $routes->import('../../src/Application/Settings/Controller/', 'attribute')
            ->prefix('/api/settings');

        // Auth API
        $routes->import('../../src/Application/Auth/Controller/', 'attribute')
            ->prefix('/api/oauth2');
    }
} 