<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Command\CreateRoleCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDescription;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleDisplayName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RolePermissions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

#[AsMessageHandler]
class CreateRoleCommandHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private DomainEventDispatcher $eventDispatcher,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $name = new RoleName($command->name);
        $displayName = new RoleDisplayName($command->displayName);
        $description = $command->description ? new RoleDescription($command->description) : null;
        $permissions = new RolePermissions($command->permissions);

        // Check if role with this name already exists
        if ($this->roleRepository->findByName($name)) {
            throw new \InvalidArgumentException('A role with this name already exists');
        }

        $role = new Role($name, $displayName, $description, $permissions);
        $this->roleRepository->save($role);
        $this->eventDispatcher->dispatch(new RoleCreated($role->id()));
        $this->eventBus->dispatch(new RoleCreatedEvent((string)$role->id(), $role->name()->value()));
    }
} 