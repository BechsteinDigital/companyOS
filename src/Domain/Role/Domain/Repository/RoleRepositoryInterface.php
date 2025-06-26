<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Repository;

use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\Role;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

interface RoleRepositoryInterface
{
    public function findById(RoleId $id): ?Role;
    
    public function findByName(RoleName $name): ?Role;
    
    public function findAll(bool $includeSystem = true, ?string $search = null): array;
    
    public function save(Role $role): void;
    
    public function delete(Role $role): void;
    
    public function findUserRoles(string $userId): array;
    
    public function assignRoleToUser(RoleId $roleId, Uuid $userId): void;
    
    public function removeRoleFromUser(Uuid $userId, RoleId $roleId): void;
    
    public function removeAllUserRoles(Uuid $userId): void;
    
    public function isRoleAssignedToUser(Uuid $userId, RoleId $roleId): bool;
    
    public function getUserCount(RoleId $roleId): int;
    
    public function findUserRole($userId, $roleId): ?\CompanyOS\Bundle\CoreBundle\Domain\Role\Domain\Entity\UserRole;
} 