<?php

namespace CompanyOS\Domain\User\Application\CommandHandler;

use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\User\Application\Command\UpdateUserCommand;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\ValueObject\Email;
use CompanyOS\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        // User-Daten aktualisieren
        $email = $command->email ? new Email($command->email) : null;
        $user->update($email, $command->firstName, $command->lastName);

        // Rollen aktualisieren, falls angegeben
        if ($command->roleIds !== null) {
            // Alle bestehenden Rollen entfernen
            $this->roleRepository->removeAllUserRoles($userId);

            // Neue Rollen zuweisen
            foreach ($command->roleIds as $roleId) {
                $role = $this->roleRepository->findById(Uuid::fromString($roleId));
                if ($role) {
                    $this->roleRepository->assignRoleToUser($role->getId(), $userId);
                }
            }
        }

        $this->userRepository->save($user);
    }
} 