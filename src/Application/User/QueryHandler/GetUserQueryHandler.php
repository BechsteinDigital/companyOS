<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\User\Query\GetUserQuery;
use CompanyOS\Bundle\CoreBundle\Application\User\DTO\UserResponse;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetUserQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {}

    public function __invoke(GetUserQuery $query): ?UserResponse
    {
        $user = $this->userRepository->findById(Uuid::fromString($query->id));
        
        if (!$user) {
            return null;
        }

        $roles = $this->roleRepository->findUserRoles((string)$user->getId());
        $roleResponses = array_map(function ($role) {
            return new RoleResponse(
                id: (string)$role->getId(),
                name: $role->getName(),
                displayName: $role->getDisplayName(),
                description: $role->getDescription(),
                permissions: $role->getPermissions(),
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
    }
} 