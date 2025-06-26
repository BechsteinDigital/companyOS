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
    }
} 