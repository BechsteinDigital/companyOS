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
                id: (string)$role->id(),
                name: $role->name()->value(),
                displayName: $role->displayName()->value(),
                description: $role->description()?->value(),
                permissions: $role->permissions()->value(),
                isSystem: $role->isSystem(),
                userCount: $this->roleRepository->getUserCount($role->id()),
                createdAt: $role->createdAt()->format('Y-m-d H:i:s'),
                updatedAt: $role->updatedAt()?->format('Y-m-d H:i:s')
            );
        }, $roles);
    }
} 