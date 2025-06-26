<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\User\Query\GetAllUsersQuery;
use CompanyOS\Bundle\CoreBundle\Application\User\DTO\UserResponse;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Role\DTO\RoleResponse;
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
            $userRoles = array_map(function ($role) {
                return [
                    'id' => (string)$role->id(),
                    'name' => $role->name()->value(),
                    'displayName' => $role->displayName()->value(),
                ];
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
                roles: $userRoles
            );
        }, $users);
    }
} 