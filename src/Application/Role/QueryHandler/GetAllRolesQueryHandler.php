<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Query\GetAllRolesQuery;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAllRolesQueryHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function __invoke(GetAllRolesQuery $query): array
    {
        $roles = $this->roleRepository->findAll($query->includeSystem, $query->search);

        return array_map(function ($role) {
            return new RoleResponse(
                id: (string)$role->getId(),
                name: $role->getName(),
                displayName: $role->getDisplayName(),
                description: $role->getDescription(),
                permissions: $role->getPermissions(),
                isSystem: $role->isSystem(),
                userCount: $this->roleRepository->getUserCount($role->getId()),
                createdAt: $role->getCreatedAt(),
                updatedAt: $role->getUpdatedAt()
            );
        }, $roles);
    }
} 