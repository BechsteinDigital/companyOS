<?php

namespace CompanyOS\Domain\Role\Application\CommandHandler;

use CompanyOS\Domain\Role\Application\Command\DeleteRoleCommand;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use CompanyOS\Domain\Role\Application\Event\RoleDeletedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Domain\Role\Application\Event\RoleDeleted;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

#[AsMessageHandler]
class DeleteRoleCommandHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private DomainEventDispatcher $eventDispatcher,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $roleId = new RoleId($command->id);
        $role = $this->roleRepository->findById($roleId);

        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        $this->roleRepository->delete($role);
        $this->eventDispatcher->dispatch(new RoleDeleted($role->getId()));
        $this->eventBus->dispatch(new RoleDeletedEvent((string)$role->getId(), $role->getName()));
    }
} 