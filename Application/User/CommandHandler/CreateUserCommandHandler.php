<?php

namespace CompanyOS\Domain\User\Application\CommandHandler;

use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\User\Application\Command\CreateUserCommand;
use CompanyOS\Domain\User\Domain\Entity\User;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\Shared\ValueObject\Email;
use CompanyOS\Domain\Shared\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        // User erstellen
        $userId = Uuid::random();
        $email = new Email($command->email);
        
        $user = new User(
            $userId,
            $email,
            $command->firstName,
            $command->lastName,
            $command->password ? $this->passwordHasher->hashPassword($user, $command->password) : null
        );

        // User speichern
        $this->userRepository->save($user);

        // Rollen zuweisen, falls angegeben
        if (!empty($command->roleIds)) {
            foreach ($command->roleIds as $roleId) {
                $role = $this->roleRepository->findById(Uuid::fromString($roleId));
                if ($role) {
                    $this->roleRepository->assignRoleToUser($role->getId(), $userId);
                }
            }
        }
    }
} 