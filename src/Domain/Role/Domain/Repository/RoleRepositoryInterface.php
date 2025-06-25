<?php

namespace CompanyOS\Domain\Role\Domain\Repository;

use CompanyOS\Domain\Role\Domain\Entity\Role;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleId;
use CompanyOS\Domain\Role\Domain\ValueObject\RoleName;
use CompanyOS\Domain\ValueObject\Uuid;

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
} 