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
            if ($role->hasPermission($permission)) {
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
            $permissions = array_merge($permissions, $role->getPermissions());
        }
        
        return array_unique($permissions);
    }

    /**
     * Enhanced permission check with context support (ABAC - Attribute-Based Access Control)
     * 
     * @param User $user The user to check permissions for
     * @param string $permission The permission to check
     * @param array $context Additional context for ABAC (time, location, department, etc.)
     * @return bool
     */
    public function hasPermissionWithContext(User $user, string $permission, array $context = []): bool
    {
        // Basic permission check first
        if (!$this->hasPermission($user, $permission)) {
            return false;
        }

        // ABAC Context-based checks
        
        // Time-based restrictions
        if (isset($context['timeRestrictions'])) {
            if (!$this->checkTimeRestrictions($context['timeRestrictions'])) {
                return false;
            }
        }

        // Department-based restrictions
        if (isset($context['departmentRestrictions'])) {
            $userDepartment = $user->getDepartment() ?? 'unknown';
            if (!in_array($userDepartment, $context['departmentRestrictions'])) {
                return false;
            }
        }

        // IP-based restrictions
        if (isset($context['ipRestrictions'])) {
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if (!$this->checkIpRestrictions($clientIp, $context['ipRestrictions'])) {
                return false;
            }
        }

        return true;
    }

    private function checkTimeRestrictions(array $timeRestrictions): bool
    {
        $now = new \DateTime();
        $currentHour = (int)$now->format('H');
        $currentWeekday = (int)$now->format('N'); // 1 = Monday, 7 = Sunday

        // Check hour restrictions
        if (isset($timeRestrictions['hours']) && is_array($timeRestrictions['hours'])) {
            [$startHour, $endHour] = $timeRestrictions['hours'];
            if ($currentHour < $startHour || $currentHour > $endHour) {
                return false;
            }
        }

        // Check weekday restrictions
        if (isset($timeRestrictions['weekdays']) && is_array($timeRestrictions['weekdays'])) {
            if (!in_array($currentWeekday, $timeRestrictions['weekdays'])) {
                return false;
            }
        }

        return true;
    }

    private function checkIpRestrictions(string $clientIp, array $allowedIpPrefixes): bool
    {
        foreach ($allowedIpPrefixes as $prefix) {
            if (str_starts_with($clientIp, $prefix)) {
                return true;
            }
        }
        return false;
    }
} 