<?php

namespace CompanyOS\Domain\Role\Application\CommandHandler;

use CompanyOS\Domain\Role\Application\Command\RemoveRoleFromUserCommand;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\Role\Application\Event\RoleRemovedFromUserEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use CompanyOS\Infrastructure\Event\DomainEventDispatcher;

#[AsMessageHandler]
class RemoveRoleFromUserCommandHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository,
        private DomainEventDispatcher $eventDispatcher,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(RemoveRoleFromUserCommand $command): void
    {
        $role = $this->roleRepository->findById($command->roleId);
        $user = $this->userRepository->findById($command->userId);
        if (!$role || !$user) {
            throw new \InvalidArgumentException('Role or user not found');
        }
        $user->removeRole($role);
        $this->userRepository->save($user);
        $this->eventDispatcher->dispatch(new RoleRemovedFromUser($role->getId(), $user->getId()));
        $this->eventBus->dispatch(new RoleRemovedFromUserEvent((string)$role->getId(), (string)$user->getId()));
    }
} 