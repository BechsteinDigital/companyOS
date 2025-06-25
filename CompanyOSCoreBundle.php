<?php

namespace CompanyOS;

use CompanyOS\DependencyInjection\CompanyOSCoreExtension;
use CompanyOS\Infrastructure\Plugin\DependencyInjection\PluginCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
} 