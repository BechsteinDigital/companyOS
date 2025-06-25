<?php

namespace CompanyOS\Domain\Plugin\Infrastructure\DependencyInjection;

use CompanyOS\Domain\Plugin\Application\CommandHandler\ActivatePluginCommandHandler;
use CompanyOS\Domain\Plugin\Application\CommandHandler\DeactivatePluginCommandHandler;
use CompanyOS\Domain\Plugin\Application\CommandHandler\DeletePluginCommandHandler;
use CompanyOS\Domain\Plugin\Application\CommandHandler\InstallPluginCommandHandler;
use CompanyOS\Domain\Plugin\Application\CommandHandler\UpdatePluginCommandHandler;
use CompanyOS\Domain\Plugin\Application\EventHandler\PluginEventHandler;
use CompanyOS\Domain\Plugin\Application\QueryHandler\CheckPluginCompatibilityQueryHandler;
use CompanyOS\Domain\Plugin\Application\QueryHandler\GetActivePluginsQueryHandler;
use CompanyOS\Domain\Plugin\Application\QueryHandler\GetAllPluginsQueryHandler;
use CompanyOS\Domain\Plugin\Application\QueryHandler\GetPluginDependenciesQueryHandler;
use CompanyOS\Domain\Plugin\Application\QueryHandler\GetPluginQueryHandler;
use CompanyOS\Domain\Plugin\Application\Service\PluginApplicationService;
use CompanyOS\Domain\Plugin\Domain\Repository\PluginRepositoryInterface;
use CompanyOS\Domain\Plugin\Domain\Service\PluginCompatibilityService;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Domain\Plugin\Infrastructure\Event\PluginEventSubscriber;
use CompanyOS\Domain\Plugin\Infrastructure\External\PluginNotificationService;
use CompanyOS\Domain\Plugin\Infrastructure\External\PluginRegistryService;
use CompanyOS\Domain\Plugin\Infrastructure\Persistence\DoctrinePluginRepository;
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