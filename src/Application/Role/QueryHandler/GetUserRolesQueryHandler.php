<?php

namespace CompanyOS\Application\Role\QueryHandler;

use CompanyOS\Application\Role\Query\GetUserRolesQuery;
use CompanyOS\Application\Role\DTO\RoleResponse;
use CompanyOS\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetUserRolesQueryHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function __invoke(GetUserRolesQuery $query): array
    {
        $roles = $this->roleRepository->findUserRoles($query->userId);

        return array_map(function ($role) {
            return new RoleResponse(
                id: (string)$role->getId(),
                name: $role->getName()->value(),
                displayName: $role->getDisplayName()->value(),
                description: $role->getDescription()->value(),
                permissions: $role->getPermissions()->value(),
                isSystem: $role->isSystem(),
                userCount: $this->roleRepository->getUserCount($role->getId()),
                createdAt: $role->getCreatedAt(),
                updatedAt: $role->getUpdatedAt()
            );
        }, $roles);
    }
} 