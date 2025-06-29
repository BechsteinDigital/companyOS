<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Command\UpdateRoleCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDescription;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDisplayName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RolePermissions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleUpdatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleUpdated;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

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
        try {
            $roleId = new RoleId($command->id);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid role ID format');
        }
        
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