<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Service;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\UserRole;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository\RoleRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function isRoleNameUnique(string $name, ?Role $excludeRole = null): bool
    {
        $existingRole = $this->roleRepository->findByName($name);
        
        if (!$existingRole) {
            return true;
        }

        if ($excludeRole && $existingRole->id()->equals($excludeRole->id())) {
            return true;
        }

        return false;
    }

    public function assignRoleToUser(Role $role, User $user): void
    {
        // Prüfen ob User bereits diese Rolle hat
        $existingUserRole = $this->roleRepository->findUserRole($user->getId(), $role->id());
        
        if ($existingUserRole) {
            throw new \InvalidArgumentException('User already has this role');
        }

        $userRole = new UserRole($user, $role);
        $this->roleRepository->saveUserRole($userRole);
    }

    public function removeRoleFromUser(Role $role, User $user): void
    {
        $userRole = $this->roleRepository->findUserRole($user->getId(), $role->id());
        
        if (!$userRole) {
            throw new \InvalidArgumentException('User does not have this role');
        }

        $this->roleRepository->removeUserRole($userRole);
    }

    public function getUserRoles(User $user): array
    {
        return $this->roleRepository->findRolesByUserId($user->getId());
    }

    public function hasRole(User $user, string $roleName): bool
    {
        $userRoles = $this->getUserRoles($user);
        
        foreach ($userRoles as $role) {
            if ($role->name()->value() === $roleName) {
                return true;
            }
        }
        
        return false;
    }

    public function hasPermission(User $user, string $permission): bool
    {
        $userRoles = $this->getUserRoles($user);
        
        foreach ($userRoles as $role) {
            if (in_array($permission, $role->permissions()->value())) {
                return true;
            }
        }
        
        return false;
    }

    public function canDeleteRole(Role $role): bool
    {
        // Prüfen ob Rolle noch Benutzern zugewiesen ist
        $userCount = $this->roleRepository->countUsersWithRole($role->id());
        
        return $userCount === 0;
    }
} 