<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Role\Query\GetRoleQuery;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetRoleQueryHandler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function __invoke(GetRoleQuery $query): ?RoleResponse
    {
        $roleId = new RoleId($query->id);
        $role = $this->roleRepository->findById($roleId);

        if (!$role) {
            return null;
        }

        try {
            $newRoleId = new RoleId((string)$role->getId());
            $userCount = $this->roleRepository->getUserCount($newRoleId);
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
    }
} 