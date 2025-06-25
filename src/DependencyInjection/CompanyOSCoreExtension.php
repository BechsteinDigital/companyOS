<?php

namespace CompanyOS\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CompanyOSCoreExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        
        // Services laden
        $loader->load('services.yaml');
        
        // Doctrine-Mappings laden
        $loader->load('doctrine.yaml');
        
        // Security-Konfiguration laden
        $loader->load('security.yaml');
        
        // Messenger-Konfiguration laden
        $loader->load('messenger.yaml');
        
        // Routing laden
        $loader->load('routes.yaml');
        
        // Bundle-Konfiguration verarbeiten
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        
        // Parameter setzen
        $container->setParameter('companyos.plugin.directories', $config['plugin']['directories']);
        $container->setParameter('companyos.auth.oauth2.enabled', $config['auth']['oauth2']['enabled']);
        $container->setParameter('companyos.auth.oauth2.access_token_ttl', $config['auth']['oauth2']['access_token_ttl']);
        $container->setParameter('companyos.auth.oauth2.refresh_token_ttl', $config['auth']['oauth2']['refresh_token_ttl']);
        $container->setParameter('companyos.webhook.enabled', $config['webhook']['enabled']);
        $container->setParameter('companyos.webhook.max_retries', $config['webhook']['max_retries']);
        $container->setParameter('companyos.webhook.timeout', $config['webhook']['timeout']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }
} 