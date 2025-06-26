<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\DependencyInjection;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Plugin Manager konfigurieren
        if ($container->hasDefinition(PluginManager::class)) {
            $pluginManager = $container->getDefinition(PluginManager::class);
            
            // Plugin Repository injizieren
            $pluginManager->addMethodCall('setPluginRepository', [
                new Reference('CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository')
            ]);
            
            // Container injizieren
            $pluginManager->addMethodCall('setContainer', [
                new Reference('service_container')
            ]);
        }

        // Plugin Service Loader konfigurieren
        if ($container->hasDefinition('CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Service\PluginServiceLoader')) {
            $serviceLoader = $container->getDefinition('CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Service\PluginServiceLoader');
        }

        // Plugin Event Subscriber konfigurieren
        if ($container->hasDefinition('CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\EventSubscriber\PluginRouteSubscriber')) {
            $routeSubscriber = $container->getDefinition('CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\EventSubscriber\PluginRouteSubscriber');
        }
    }
} 