<?php

namespace CompanyOS\Infrastructure\Plugin\Compiler;

use CompanyOS\Infrastructure\Plugin\Service\PluginServiceLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Only process if we're not in cache warmup
        if (!$container->hasParameter('kernel.environment')) {
            return;
        }

        // Load plugin services
        if ($container->has('CompanyOS\Core\Plugin\Infrastructure\Service\PluginServiceLoader')) {
            $serviceLoader = $container->get('CompanyOS\Core\Plugin\Infrastructure\Service\PluginServiceLoader');
            $serviceLoader->loadPluginServices($container);
        }
    }
} 