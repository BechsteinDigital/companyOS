<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Query\GetUserRolesQuery;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
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
            try {
                $roleId = new RoleId((string)$role->getId());
                $userCount = $this->roleRepository->getUserCount($roleId);
            } catch (\Exception $e) {
                $userCount = 0; // Fallback if UUID validation fails
            }
            
            return new RoleResponse(
                id: (string)$role->getId(),
                name: $role->getName(),
                displayName: $role->getDisplayName(),
                description: $role->getDescription(),
                permissions: $role->getPermissions(),
                isSystem: $role->isSystem(),
                userCount: $userCount,
                createdAt: $role->getCreatedAt(),
                updatedAt: $role->getUpdatedAt()
            );
        }, $roles);
    }
} 