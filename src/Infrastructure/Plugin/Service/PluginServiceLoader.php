<?php

namespace CompanyOS\Infrastructure\Plugin\Service;

use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Psr\Log\LoggerInterface;

class PluginServiceLoader
{
    public function __construct(
        private PluginManager $pluginManager,
        private LoggerInterface $logger
    ) {
    }

    public function loadPluginServices(ContainerBuilder $container): void
    {
        $loadedPlugins = $this->pluginManager->getLoadedPlugins();

        foreach ($loadedPlugins as $plugin) {
            $this->loadPluginServiceConfiguration($container, $plugin);
        }
    }

    private function loadPluginServiceConfiguration(ContainerBuilder $container, $plugin): void
    {
        $servicesPath = $plugin->getPath() . '/Resources/config/services.yaml';
        
        if (file_exists($servicesPath)) {
            try {
                $loader = new YamlFileLoader($container, new FileLocator());
                $loader->load($servicesPath);
            } catch (\Exception $e) {
                // Log error but don't break the application
                $this->logger->error("Failed to load services for plugin {$plugin->getPluginName()}: " . $e->getMessage());
            }
        }
    }
} 