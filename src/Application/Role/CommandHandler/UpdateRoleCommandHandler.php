<?php

namespace CompanyOS\Application\Role\CommandHandler;

use CompanyOS\Application\Role\Command\UpdateRoleCommand;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleDescription;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleDisplayName;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\Role\Domain\ValueObject\RolePermissions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use CompanyOS\Application\Role\Event\RoleUpdatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Application\Role\Event\RoleUpdated;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

#[AsMessageHandler]
class UpdateRoleCommandHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private DomainEventDispatcher $eventDispatcher,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $roleId = new RoleId($command->id);
        $role = $this->roleRepository->findById($roleId);

        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        if ($command->displayName !== null) {
            $role->updateDisplayName(new RoleDisplayName($command->displayName));
        }

        if ($command->description !== null) {
            $role->updateDescription(new RoleDescription($command->description));
        }

        if ($command->permissions !== null) {
            $role->updatePermissions(new RolePermissions($command->permissions));
        }

        $this->roleRepository->save($role);
        $this->eventDispatcher->dispatch(new RoleUpdated($role->getId()));
        $this->eventBus->dispatch(new RoleUpdatedEvent((string)$role->getId(), $role->getName()));
    }
} 