<?php

namespace CompanyOS\Domain\Role\Application\EventHandler;

use CompanyOS\Domain\Role\Application\Event\RoleCreatedEvent;
use CompanyOS\Domain\Role\Application\Event\RoleUpdatedEvent;
use CompanyOS\Domain\Role\Application\Event\RoleDeletedEvent;
use CompanyOS\Domain\Role\Application\Event\RoleAssignedToUserEvent;
use CompanyOS\Domain\Role\Application\Event\RoleRemovedFromUserEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class RoleEventHandler
{
    public function __construct(private LoggerInterface $logger) {}

    #[AsMessageHandler]
    public function onRoleCreated(RoleCreatedEvent $event): void
    {
        $this->logger->info('Rolle erstellt: ' . $event->name . ' (' . $event->roleId . ')');
    }

    #[AsMessageHandler]
    public function onRoleUpdated(RoleUpdatedEvent $event): void
    {
        $this->logger->info('Rolle aktualisiert: ' . $event->name . ' (' . $event->roleId . ')');
    }

    #[AsMessageHandler]
    public function onRoleDeleted(RoleDeletedEvent $event): void
    {
        $this->logger->info('Rolle gelöscht: ' . $event->name . ' (' . $event->roleId . ')');
    }

    #[AsMessageHandler]
    public function onRoleAssignedToUser(RoleAssignedToUserEvent $event): void
    {
        $this->logger->info('Rolle ' . $event->roleId . ' zu Benutzer ' . $event->userId . ' zugewiesen');
    }

    #[AsMessageHandler]
    public function onRoleRemovedFromUser(RoleRemovedFromUserEvent $event): void
    {
        $this->logger->info('Rolle ' . $event->roleId . ' von Benutzer ' . $event->userId . ' entfernt');
    }
} 