<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Service;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;

class PermissionService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function hasPermission(User $user, string $permission): bool
    {
        $userId = (string)$user->getId();
        $userRoles = $this->roleRepository->findUserRoles($userId);
        
        foreach ($userRoles as $role) {
            if ($role->permissions()->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    public function hasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($user, $permission)) {
                return true;
            }
        }
        
        return false;
    }

    public function hasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($user, $permission)) {
                return false;
            }
        }
        
        return true;
    }

    public function getUserPermissions(User $user): array
    {
        $userId = (string)$user->getId();
        $userRoles = $this->roleRepository->findUserRoles($userId);
        $permissions = [];
        
        foreach ($userRoles as $role) {
            $permissions = array_merge($permissions, $role->permissions()->value());
        }
        
        return array_unique($permissions);
    }
} 