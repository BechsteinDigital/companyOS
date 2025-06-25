<?php

declare(strict_types=1);

namespace CompanyOS\Application\Plugin\EventHandler;

use CompanyOS\Application\Plugin\Event\PluginActivatedEvent;
use CompanyOS\Application\Plugin\Event\PluginDeletedEvent;
use CompanyOS\Application\Plugin\Event\PluginDeactivatedEvent;
use CompanyOS\Application\Plugin\Event\PluginInstallationFailedEvent;
use CompanyOS\Application\Plugin\Event\PluginInstalledEvent;
use CompanyOS\Application\Plugin\Event\PluginUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PluginEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(PluginInstalledEvent $event): void
    {
        $this->logger->info('Plugin installed successfully', [
            'pluginId' => $event->pluginId->value(),
            'pluginName' => $event->pluginName,
            'version' => $event->version,
            'author' => $event->author,
            'dependencies' => $event->dependencies
        ]);
    }

    public function handlePluginActivated(PluginActivatedEvent $event): void
    {
        $this->logger->info('Plugin activated', [
            'pluginId' => $event->pluginId->value(),
            'pluginName' => $event->pluginName,
            'version' => $event->version
        ]);
    }

    public function handlePluginDeactivated(PluginDeactivatedEvent $event): void
    {
        $this->logger->info('Plugin deactivated', [
            'pluginId' => $event->pluginId->value(),
            'pluginName' => $event->pluginName,
            'version' => $event->version
        ]);
    }

    public function handlePluginUpdated(PluginUpdatedEvent $event): void
    {
        $this->logger->info('Plugin updated', [
            'pluginId' => $event->pluginId->value(),
            'pluginName' => $event->pluginName,
            'oldVersion' => $event->oldVersion,
            'newVersion' => $event->newVersion,
            'changelog' => $event->changelog
        ]);
    }

    public function handlePluginDeleted(PluginDeletedEvent $event): void
    {
        $this->logger->info('Plugin deleted', [
            'pluginId' => $event->pluginId->value(),
            'pluginName' => $event->pluginName,
            'version' => $event->version
        ]);
    }

    public function handlePluginInstallationFailed(PluginInstallationFailedEvent $event): void
    {
        $this->logger->error('Plugin installation failed', [
            'pluginName' => $event->pluginName,
            'version' => $event->version,
            'reason' => $event->reason,
            'errors' => $event->errors
        ]);
    }
} 