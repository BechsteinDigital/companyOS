<?php

namespace CompanyOS\Infrastructure\Plugin\DependencyInjection;

use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
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
                new Reference('CompanyOS\Domain\Plugin\Domain\Repository\PluginRepository')
            ]);
            
            // Container injizieren
            $pluginManager->addMethodCall('setContainer', [
                new Reference('service_container')
            ]);
        }

        // Plugin Service Loader konfigurieren
        if ($container->hasDefinition('CompanyOS\Infrastructure\Plugin\Service\PluginServiceLoader')) {
            $serviceLoader = $container->getDefinition('CompanyOS\Infrastructure\Plugin\Service\PluginServiceLoader');
            
            // Plugin Manager injizieren
            $serviceLoader->addMethodCall('setPluginManager', [
                new Reference(PluginManager::class)
            ]);
        }

        // Plugin Event Subscriber konfigurieren
        if ($container->hasDefinition('CompanyOS\Infrastructure\Plugin\EventSubscriber\PluginRouteSubscriber')) {
            $routeSubscriber = $container->getDefinition('CompanyOS\Infrastructure\Plugin\EventSubscriber\PluginRouteSubscriber');
            
            // Plugin Manager injizieren
            $routeSubscriber->addMethodCall('setPluginManager', [
                new Reference(PluginManager::class)
            ]);
        }
    }
} 