<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Security\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;

/**
 * Access Control List (ACL) Service
 * 
 * Verwaltet direkte Object-level Permissions:
 * - File/Document Ownership
 * - Share-Permissions
 * - Explizite Grant/Deny Lists
 * - Inheritance von Parent-Objects
 */
class AclService
{
    public function __construct(
        private readonly AclRepositoryInterface $aclRepository
    ) {
    }

    /**
     * Prüft ACL-Berechtigung für eine Resource
     * 
     * @return bool|null true=allow, false=deny, null=neutral
     */
    public function hasAccess(User $user, string $permission, $resource): ?bool
    {
        // 1. Ownership Check (höchste Priorität für eigene Resources)
        if ($this->isOwner($user, $resource)) {
            $ownerPermissions = $this->getOwnerPermissions($resource);
            if (in_array($permission, $ownerPermissions)) {
                return true; // Owner hat Vollzugriff
            }
        }
        
        // 2. Explizite Share-Permissions prüfen
        $sharePermissions = $this->getSharePermissions($user, $resource);
        if (isset($sharePermissions[$permission])) {
            return $sharePermissions[$permission]; // true oder false
        }
        
        // 3. Public/Default Permissions
        $publicPermissions = $this->getPublicPermissions($resource);
        if (in_array($permission, $publicPermissions)) {
            return true;
        }
        
        // Kein ACL-Match = neutral (RBAC/ABAC entscheiden)
        return null;
    }
    
    /**
     * Detaillierte ACL-Prüfung mit Begründung
     */
    public function hasAccessDetailed(User $user, string $permission, $resource): array
    {
        $result = [
            'decision' => 'neutral',
            'reason' => 'No ACL rules apply',
            'source' => 'none'
        ];
        
        // 1. Ownership
        if ($this->isOwner($user, $resource)) {
            $ownerPermissions = $this->getOwnerPermissions($resource);
            if (in_array($permission, $ownerPermissions)) {
                $result['decision'] = 'allow';
                $result['reason'] = 'Resource owner permissions';
                $result['source'] = 'ownership';
                return $result;
            }
        }
        
        // 2. Share-Permissions
        $sharePermissions = $this->getSharePermissions($user, $resource);
        if (isset($sharePermissions[$permission])) {
            $result['decision'] = $sharePermissions[$permission] ? 'allow' : 'deny';
            $result['reason'] = 'Explicit share permission';
            $result['source'] = 'sharing';
            return $result;
        }
        
        // 3. Public
        $publicPermissions = $this->getPublicPermissions($resource);
        if (in_array($permission, $publicPermissions)) {
            $result['decision'] = 'allow';
            $result['reason'] = 'Public resource access';
            $result['source'] = 'public';
            return $result;
        }
        
        return $result;
    }
    
    /**
     * ACL-Entry erstellen
     */
    public function grantPermission(User $user, $resource, string $permission, ?User $grantedBy = null): void
    {
        $entry = [
            'user_id' => $user->getId()->value(),
            'resource_id' => $this->getResourceId($resource),
            'resource_type' => $this->getResourceType($resource),
            'permission' => $permission,
            'type' => 'allow',
            'granted_by' => $grantedBy?->getId()->value(),
            'granted_at' => new \DateTimeImmutable()
        ];
        
        $this->aclRepository->createEntry($entry);
    }
    
    /**
     * ACL-Entry entziehen
     */
    public function denyPermission(User $user, $resource, string $permission, ?User $deniedBy = null): void
    {
        $entry = [
            'user_id' => $user->getId()->value(),
            'resource_id' => $this->getResourceId($resource),
            'resource_type' => $this->getResourceType($resource),
            'permission' => $permission,
            'type' => 'deny',
            'granted_by' => $deniedBy?->getId()->value(),
            'granted_at' => new \DateTimeImmutable()
        ];
        
        $this->aclRepository->createEntry($entry);
    }
    
    /**
     * ACL-Entry löschen
     */
    public function revokePermission(User $user, $resource, string $permission): void
    {
        $this->aclRepository->deleteEntry(
            $user->getId()->value(),
            $this->getResourceId($resource),
            $this->getResourceType($resource),
            $permission
        );
    }
    
    /**
     * Alle Permissions für eine Resource abrufen
     */
    public function getResourcePermissions($resource): array
    {
        $resourceId = $this->getResourceId($resource);
        $resourceType = $this->getResourceType($resource);
        
        return $this->aclRepository->findResourcePermissions($resourceId, $resourceType);
    }
    
    /**
     * Alle Permissions für einen User abrufen
     */
    public function getUserPermissions(User $user, string $resourceType = null): array
    {
        return $this->aclRepository->findUserPermissions(
            $user->getId()->value(),
            $resourceType
        );
    }
    
    private function isOwner(User $user, $resource): bool
    {
        if (method_exists($resource, 'getOwnerId')) {
            return $resource->getOwnerId() === $user->getId()->value();
        }
        
        if (method_exists($resource, 'getCreatedBy')) {
            return $resource->getCreatedBy()?->getId()->value() === $user->getId()->value();
        }
        
        return false;
    }
    
    private function getOwnerPermissions($resource): array
    {
        // Owner hat standardmäßig alle Permissions
        return ['read', 'write', 'update', 'delete', 'share', 'admin'];
    }
    
    private function getSharePermissions(User $user, $resource): array
    {
        // Simulierte Share-Permissions
        // In echter Implementierung: DB-Abfrage nach Share-Tabelle
        
        if (method_exists($resource, 'getSharedWith')) {
            $shares = $resource->getSharedWith();
            $userId = $user->getId()->value();
            
            return $shares[$userId] ?? [];
        }
        
        return [];
    }
    
    private function getPublicPermissions($resource): array
    {
        if (method_exists($resource, 'isPublic') && $resource->isPublic()) {
            return ['read']; // Public resources sind nur lesbar
        }
        
        return [];
    }
    
    private function getResourceId($resource): string
    {
        if (is_string($resource)) {
            return $resource;
        }
        
        if (method_exists($resource, 'getId')) {
            $id = $resource->getId();
            return is_object($id) ? $id->value() : (string) $id;
        }
        
        throw new \InvalidArgumentException('Cannot determine resource ID');
    }
    
    private function getResourceType($resource): string
    {
        if (is_object($resource)) {
            return strtolower((new \ReflectionClass($resource))->getShortName());
        }
        
        return 'unknown';
    }
    
    private function getUserGroups(User $user): array
    {
        // User's Groups/Teams laden
        return []; // Mock
    }
    
    private function getResourceGroups($resource): array
    {
        // Resource's Groups/Teams laden
        return []; // Mock
    }
    
    private function getGroupPermissions(string $groupId, $resource): array
    {
        // Group-Permissions für Resource laden
        return []; // Mock
    }
    
    private function getParentResource($resource)
    {
        if (method_exists($resource, 'getParent')) {
            return $resource->getParent();
        }
        
        return null;
    }
    
    private function isInheritanceEnabled($resource): bool
    {
        if (method_exists($resource, 'isInheritanceEnabled')) {
            return $resource->isInheritanceEnabled();
        }
        
        return true; // Default: Inheritance enabled
    }
} 