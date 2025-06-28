<?php

namespace CompanyOS\Bundle\CoreBundle\DependencyInjection;

use CompanyOS\Bundle\CoreBundle\DependencyInjection\Compiler\OAuth2UserConverterCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Doctrine\DBAL\Types\Type;

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
        
        // Doctrine-Konfiguration nur laden, wenn DoctrineBundle aktiv ist
        if ($container->hasExtension('doctrine')) {
            $loader->load('packages/doctrine.yaml');
            
            // Doctrine-Types registrieren
            if (!Type::hasType('uuid')) {
                Type::addType('uuid', \CompanyOS\Bundle\CoreBundle\Infrastructure\Persistence\Doctrine\UuidType::class);
            }
            if (!Type::hasType('email')) {
                Type::addType('email', \CompanyOS\Bundle\CoreBundle\Infrastructure\Persistence\Doctrine\EmailType::class);
            }
        }
        
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
        
        // Compiler Passes registrieren
        $container->addCompilerPass(new OAuth2UserConverterCompilerPass());
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }
} 