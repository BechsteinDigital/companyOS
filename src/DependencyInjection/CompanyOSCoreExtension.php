<?php

namespace CompanyOS\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CompanyOSCoreExtension extends Extension
{
    public function getAlias(): string
    {
        return 'company_os_core';
    }
    
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        
        // Services laden (immer verfügbar)
        $loader->load('services.yaml');
        
        // Doctrine-Konfiguration laden
        $loader->load('packages/doctrine.yaml');
        
        // Security-Konfiguration nur laden, wenn Security-Extension verfügbar ist
        if ($container->hasExtension('security')) {
            $loader->load('security.yaml');
        }
        
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