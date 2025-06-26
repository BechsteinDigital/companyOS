<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Command\AssignRoleToUserCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Role\Event\RoleAssignedToUserEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;

#[AsMessageHandler]
class AssignRoleToUserCommandHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository,
        private DomainEventDispatcher $eventDispatcher,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(AssignRoleToUserCommand $command): void
    {
        $role = $this->roleRepository->findById($command->roleId);
        $user = $this->userRepository->findById($command->userId);
        if (!$role || !$user) {
            throw new \InvalidArgumentException('Role or user not found');
        }
        $user->assignRole($role);
        $this->userRepository->save($user);
        $this->eventDispatcher->dispatch(new RoleAssignedToUser($role->id(), $user->getId()));
        $this->eventBus->dispatch(new RoleAssignedToUserEvent((string)$role->id(), (string)$user->getId()));
    }
} 