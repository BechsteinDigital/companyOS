<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\DependencyInjection;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler\ActivatePluginCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler\DeactivatePluginCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler\DeletePluginCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler\InstallPluginCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler\UpdatePluginCommandHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\EventHandler\PluginEventHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler\CheckPluginCompatibilityQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler\GetActivePluginsQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler\GetAllPluginsQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler\GetPluginDependenciesQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler\GetPluginQueryHandler;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\Service\PluginApplicationService;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginCompatibilityService;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Event\PluginEventSubscriber;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\External\PluginNotificationService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\External\PluginRegistryService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Persistence\DoctrinePluginRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class PluginExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        // Repository Bindings
        $container->setAlias(PluginRepositoryInterface::class, DoctrinePluginRepository::class);

        // Command Handlers
        $container->autowire(InstallPluginCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(UpdatePluginCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(ActivatePluginCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(DeactivatePluginCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        $container->autowire(DeletePluginCommandHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'command_bus']);

        // Query Handlers
        $container->autowire(GetAllPluginsQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(GetPluginQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(GetActivePluginsQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(GetPluginDependenciesQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        $container->autowire(CheckPluginCompatibilityQueryHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'query.bus']);

        // Event Handlers
        $container->autowire(PluginEventHandler::class)
            ->addTag('messenger.message_handler', ['bus' => 'event.bus']);

        // Event Subscribers
        $container->autowire(PluginEventSubscriber::class)
            ->addTag('kernel.event_subscriber');

        // Domain Services
        $container->autowire(PluginCompatibilityService::class)
            ->setPublic(true);

        // External Services
        $container->autowire(PluginRegistryService::class)
            ->setPublic(true);

        $container->autowire(PluginNotificationService::class)
            ->setPublic(true);

        // Application Service
        $container->autowire(PluginApplicationService::class)
            ->setPublic(true);
    }
} 