<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Security\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Application\Role\Service\PermissionService;
use CompanyOS\Bundle\CoreBundle\Application\Role\Service\AbacService;

/**
 * Hybrid Access Control Service
 * 
 * Kombiniert ACL, RBAC und ABAC für optimale Security:
 * 
 * Layer 1: ACL - Direkte Object-level Permissions (Files, Documents, etc.)
 * Layer 2: RBAC - Feature-level Permissions (User-Management, etc.)  
 * Layer 3: ABAC - Kontext-sensitive Regeln (Zeit, Ort, etc.)
 */
class HybridAccessControlService
{
    public function __construct(
        private readonly AclService $aclService,
        private readonly PermissionService $rbacService,
        private readonly AbacService $abacService
    ) {
    }

    /**
     * Universal Access Check - kombiniert alle drei Ansätze
     */
    public function hasAccess(
        User $user, 
        string $permission, 
        $resource = null, 
        array $context = []
    ): bool {
        // Layer 1: ACL Check (falls Resource vorhanden)
        if ($resource !== null) {
            $aclResult = $this->aclService->hasAccess($user, $permission, $resource);
            
            // ACL DENY = sofortiger Stopp
            if ($aclResult === false) {
                return false;
            }
            
            // ACL ALLOW = Zugriff gewährt (skip andere Checks)
            if ($aclResult === true) {
                return true;
            }
            
            // ACL NEUTRAL = weitermachen mit RBAC/ABAC
        }
        
        // Layer 2: RBAC Check (Feature-level)
        $rbacResult = $this->rbacService->hasPermission($user, $permission);
        
        if (!$rbacResult) {
            return false; // Keine Basis-Berechtigung
        }
        
        // Admin-Überschreibung (skip ABAC)
        if ($this->rbacService->hasRole($user, 'ROLE_ADMIN')) {
            return true;
        }
        
        // Layer 3: ABAC Check (Kontext-sensitive)
        return $this->abacService->checkContextRules($user, $permission, $context);
    }
    
    /**
     * Empfohlene Access-Checks für verschiedene Szenarien
     */
    public function canViewFile(User $user, $file): bool
    {
        return $this->hasAccess(
            user: $user,
            permission: 'file.read',
            resource: $file,
            context: [
                'file.path' => $file->getPath(),
                'file.owner' => $file->getOwnerId(),
                'file.sensitivity' => $file->getSensitivityLevel()
            ]
        );
    }
    
    public function canEditDocument(User $user, $document): bool
    {
        return $this->hasAccess(
            user: $user,
            permission: 'document.update',
            resource: $document,
            context: [
                'document.status' => $document->getStatus(),
                'document.department' => $document->getDepartment(),
                'edit.collaborative' => $document->isCollaborative()
            ]
        );
    }
    
    public function canManageUsers(User $user): bool
    {
        // Kein spezifisches Resource = nur RBAC + ABAC
        return $this->hasAccess(
            user: $user,
            permission: 'user.manage',
            resource: null,
            context: [
                'action.scope' => 'global',
                'action.risk_level' => 'high'
            ]
        );
    }
    
    public function canDeleteUser(User $user, User $targetUser): bool
    {
        return $this->hasAccess(
            user: $user,
            permission: 'user.delete',
            resource: $targetUser,
            context: [
                'target.department' => $targetUser->getDepartment(),
                'target.role_level' => $this->getUserRoleLevel($targetUser),
                'action.risk_level' => 'critical'
            ]
        );
    }
    
    /**
     * Batch Access Check für Performance
     */
    public function checkMultipleAccess(
        User $user, 
        array $permissions, 
        $resource = null, 
        array $context = []
    ): array {
        $results = [];
        
        foreach ($permissions as $permission) {
            $results[$permission] = $this->hasAccess($user, $permission, $resource, $context);
        }
        
        return $results;
    }
    
    /**
     * Access mit detailliertem Grund (für Debugging/Auditing)
     */
    public function hasAccessDetailed(
        User $user, 
        string $permission, 
        $resource = null, 
        array $context = []
    ): array {
        $result = [
            'allowed' => false,
            'layers' => [],
            'reason' => '',
            'checked_at' => new \DateTimeImmutable()
        ];
        
        // Layer 1: ACL
        if ($resource !== null) {
            $aclResult = $this->aclService->hasAccessDetailed($user, $permission, $resource);
            $result['layers']['acl'] = $aclResult;
            
            if ($aclResult['decision'] === 'deny') {
                $result['reason'] = 'ACL: ' . $aclResult['reason'];
                return $result;
            }
            
            if ($aclResult['decision'] === 'allow') {
                $result['allowed'] = true;
                $result['reason'] = 'ACL: ' . $aclResult['reason'];
                return $result;
            }
        }
        
        // Layer 2: RBAC
        $rbacResult = $this->rbacService->hasPermissionDetailed($user, $permission);
        $result['layers']['rbac'] = $rbacResult;
        
        if (!$rbacResult['allowed']) {
            $result['reason'] = 'RBAC: ' . $rbacResult['reason'];
            return $result;
        }
        
        // Admin-Überschreibung
        if ($this->rbacService->hasRole($user, 'ROLE_ADMIN')) {
            $result['allowed'] = true;
            $result['reason'] = 'RBAC: Admin override';
            return $result;
        }
        
        // Layer 3: ABAC
        $abacResult = $this->abacService->checkContextRulesDetailed($user, $permission, $context);
        $result['layers']['abac'] = $abacResult;
        
        $result['allowed'] = $abacResult['allowed'];
        $result['reason'] = 'ABAC: ' . $abacResult['reason'];
        
        return $result;
    }
    
    private function getUserRoleLevel(User $user): int
    {
        // Vereinfachte Rolle-Level Berechnung
        $roles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $roles)) return 5;
        if (in_array('ROLE_MANAGER', $roles)) return 4;
        if (in_array('ROLE_SUPERVISOR', $roles)) return 3;
        if (in_array('ROLE_EMPLOYEE', $roles)) return 2;
        if (in_array('ROLE_USER', $roles)) return 1;
        
        return 0;
    }
} 