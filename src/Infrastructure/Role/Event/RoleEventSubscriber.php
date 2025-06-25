<?php

namespace CompanyOS\Domain\Role\Infrastructure\Event;

use CompanyOS\Domain\Role\Domain\Event\RoleCreated;
use CompanyOS\Domain\Role\Domain\Event\RoleUpdated;
use CompanyOS\Domain\Role\Domain\Event\RoleDeleted;
use CompanyOS\Domain\Role\Domain\Event\RoleAssignedToUser;
use CompanyOS\Domain\Role\Domain\Event\RoleRemovedFromUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoleEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [
            RoleCreated::class => 'onRoleCreated',
            RoleUpdated::class => 'onRoleUpdated',
            RoleDeleted::class => 'onRoleDeleted',
            RoleAssignedToUser::class => 'onRoleAssignedToUser',
            RoleRemovedFromUser::class => 'onRoleRemovedFromUser',
        ];
    }

    public function onRoleCreated(RoleCreated $event): void
    {
        $this->logger->info('Domain-Event: Rolle erstellt: ' . $event->roleId);
    }
    public function onRoleUpdated(RoleUpdated $event): void
    {
        $this->logger->info('Domain-Event: Rolle aktualisiert: ' . $event->roleId);
    }
    public function onRoleDeleted(RoleDeleted $event): void
    {
        $this->logger->info('Domain-Event: Rolle gelöscht: ' . $event->roleId);
    }
    public function onRoleAssignedToUser(RoleAssignedToUser $event): void
    {
        $this->logger->info('Domain-Event: Rolle ' . $event->roleId . ' zu Benutzer ' . $event->userId . ' zugewiesen');
    }
    public function onRoleRemovedFromUser(RoleRemovedFromUser $event): void
    {
        $this->logger->info('Domain-Event: Rolle ' . $event->roleId . ' von Benutzer ' . $event->userId . ' entfernt');
    }
} 