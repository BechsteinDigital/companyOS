<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Command\DeleteRoleCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleDeletedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleDeleted;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

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
        try {
            $roleId = new RoleId($command->id);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid role ID format');
        }
        
        $role = $this->roleRepository->findById($roleId);

        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        $this->roleRepository->delete($role);
        $this->eventDispatcher->dispatch(new RoleDeleted($role->getId()));
        $this->eventBus->dispatch(new RoleDeletedEvent((string)$role->getId(), $role->getName()));
    }
} 