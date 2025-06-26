<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\EventHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleCreatedEvent;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleUpdatedEvent;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleDeletedEvent;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleAssignedToUserEvent;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleRemovedFromUserEvent;
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