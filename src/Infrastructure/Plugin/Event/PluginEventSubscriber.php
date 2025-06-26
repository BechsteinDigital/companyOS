<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginActivated;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginDeactivated;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginDeleted;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginInstalled;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginUpdated;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\External\PluginNotificationService;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Plugin\External\PluginRegistryService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PluginEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PluginRegistryService $registryService,
        private readonly PluginNotificationService $notificationService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginInstalled::class => [
                ['onPluginInstalled', 10],
                ['logPluginInstalled', 5],
            ],
            PluginActivated::class => [
                ['onPluginActivated', 10],
                ['logPluginActivated', 5],
            ],
            PluginDeactivated::class => [
                ['onPluginDeactivated', 10],
                ['logPluginDeactivated', 5],
            ],
            PluginUpdated::class => [
                ['onPluginUpdated', 10],
                ['logPluginUpdated', 5],
            ],
            PluginDeleted::class => [
                ['onPluginDeleted', 10],
                ['logPluginDeleted', 5],
            ],
        ];
    }

    public function onPluginInstalled(PluginInstalled $event): void
    {
        // Plugin in Registry registrieren
        $this->registryService->registerPlugin(
            $event->getPluginId()->value(),
            $event->getPluginName()->value(),
            $event->getVersion()->value(),
            $event->getAuthor()->value()
        );

        // Cache invalidieren
        $this->registryService->invalidateCache();

        // Benachrichtigung senden
        $this->notificationService->sendPluginInstalledNotification(
            $event->getPluginName()->value(),
            $event->getVersion()->value(),
            $event->getAuthor()->value()
        );
    }

    public function onPluginActivated(PluginActivated $event): void
    {
        // Plugin in Registry als aktiv markieren
        $this->registryService->activatePlugin($event->getPluginId()->value());

        // Cache invalidieren
        $this->registryService->invalidateCache();

        // Benachrichtigung senden
        $this->notificationService->sendPluginActivatedNotification(
            $event->getPluginName()->value(),
            $event->getVersion()->value()
        );
    }

    public function onPluginDeactivated(PluginDeactivated $event): void
    {
        // Plugin in Registry als inaktiv markieren
        $this->registryService->deactivatePlugin($event->getPluginId()->value());

        // Cache invalidieren
        $this->registryService->invalidateCache();

        // Benachrichtigung senden
        $this->notificationService->sendPluginDeactivatedNotification(
            $event->getPluginName()->value(),
            $event->getVersion()->value()
        );
    }

    public function onPluginUpdated(PluginUpdated $event): void
    {
        // Plugin in Registry aktualisieren
        $this->registryService->updatePlugin(
            $event->getPluginId()->value(),
            $event->getNewVersion()->value(),
            $event->getChangelog()
        );

        // Cache invalidieren
        $this->registryService->invalidateCache();

        // Benachrichtigung senden
        $this->notificationService->sendPluginUpdatedNotification(
            $event->getPluginName()->value(),
            $event->getOldVersion()->value(),
            $event->getNewVersion()->value()
        );
    }

    public function onPluginDeleted(PluginDeleted $event): void
    {
        // Plugin aus Registry entfernen
        $this->registryService->unregisterPlugin($event->getPluginId()->value());

        // Cache invalidieren
        $this->registryService->invalidateCache();

        // Benachrichtigung senden
        $this->notificationService->sendPluginDeletedNotification(
            $event->getPluginName()->value(),
            $event->getVersion()->value()
        );
    }

    public function logPluginInstalled(PluginInstalled $event): void
    {
        $this->logger->info('Plugin installed', [
            'pluginId' => $event->getPluginId()->value(),
            'pluginName' => $event->getPluginName()->value(),
            'version' => $event->getVersion()->value(),
            'author' => $event->getAuthor()->value(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logPluginActivated(PluginActivated $event): void
    {
        $this->logger->info('Plugin activated', [
            'pluginId' => $event->getPluginId()->value(),
            'pluginName' => $event->getPluginName()->value(),
            'version' => $event->getVersion()->value(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logPluginDeactivated(PluginDeactivated $event): void
    {
        $this->logger->info('Plugin deactivated', [
            'pluginId' => $event->getPluginId()->value(),
            'pluginName' => $event->getPluginName()->value(),
            'version' => $event->getVersion()->value(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logPluginUpdated(PluginUpdated $event): void
    {
        $this->logger->info('Plugin updated', [
            'pluginId' => $event->getPluginId()->value(),
            'pluginName' => $event->getPluginName()->value(),
            'oldVersion' => $event->getOldVersion()->value(),
            'newVersion' => $event->getNewVersion()->value(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function logPluginDeleted(PluginDeleted $event): void
    {
        $this->logger->info('Plugin deleted', [
            'pluginId' => $event->getPluginId()->value(),
            'pluginName' => $event->getPluginName()->value(),
            'version' => $event->getVersion()->value(),
            'timestamp' => $event->getOccurredAt()->format('Y-m-d H:i:s')
        ]);
    }
} 