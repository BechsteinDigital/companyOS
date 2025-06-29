<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Query\GetAllRolesQuery;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
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

        $validRoles = [];
        
        foreach ($roles as $role) {
            try {
                // Validate that we can create a RoleId from the role's ID
                $roleId = new RoleId((string)$role->getId());
                $userCount = $this->roleRepository->getUserCount($roleId);
                
                $validRoles[] = new RoleResponse(
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
            } catch (\Exception $e) {
                // Skip invalid roles - log the error but don't fail the entire request
                error_log("Skipping invalid role with ID: " . ($role->getId() ?? 'null') . " - Error: " . $e->getMessage());
                continue;
            }
        }
        
        return $validRoles;
    }
} 