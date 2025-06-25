<?php

namespace CompanyOS\Domain\Role\Application\CommandHandler;

use CompanyOS\Domain\Role\Application\Command\AssignRoleToUserCommand;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\Role\Application\Event\RoleAssignedToUserEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

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
        $this->eventDispatcher->dispatch(new RoleAssignedToUser($role->getId(), $user->getId()));
        $this->eventBus->dispatch(new RoleAssignedToUserEvent((string)$role->getId(), (string)$user->getId()));
    }
} 