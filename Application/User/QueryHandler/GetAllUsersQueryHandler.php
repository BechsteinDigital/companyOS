<?php

namespace CompanyOS\Domain\User\Application\QueryHandler;

use CompanyOS\Domain\User\Application\Query\GetAllUsersQuery;
use CompanyOS\Domain\User\Application\DTO\UserResponse;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Domain\Role\Application\DTO\RoleResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAllUsersQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {}

    public function __invoke(GetAllUsersQuery $query): array
    {
        $users = $query->activeOnly 
            ? $this->userRepository->findActive()
            : $this->userRepository->findAll();

        return array_map(function ($user) {
            $roles = $this->roleRepository->findUserRoles((string)$user->getId());
            $roleResponses = array_map(function ($role) {
                return new RoleResponse(
                    id: (string)$role->getId(),
                    name: $role->getName()->value(),
                    displayName: $role->getDisplayName()->value(),
                    description: $role->getDescription()->value(),
                    permissions: $role->getPermissions()->value(),
                    isSystem: $role->isSystem(),
                    createdAt: $role->getCreatedAt(),
                    updatedAt: $role->getUpdatedAt()
                );
            }, $roles);

            return new UserResponse(
                id: (string)$user->getId(),
                email: $user->getEmail()->toString(),
                firstName: $user->getFirstName(),
                lastName: $user->getLastName(),
                fullName: $user->getFirstName() . ' ' . $user->getLastName(),
                isActive: $user->isActive(),
                createdAt: $user->getCreatedAt(),
                updatedAt: $user->getUpdatedAt(),
                roles: $roleResponses
            );
        }, $users);
    }
} 