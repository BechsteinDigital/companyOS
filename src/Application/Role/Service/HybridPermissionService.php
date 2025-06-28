<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Service;

use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Entity\User;
use CompanyOS\Bundle\CoreBundle\Application\Role\Service\PermissionService;

/**
 * Hybrid Permission Service - Kombiniert RBAC und ABAC
 * 
 * RBAC: Basis-Permissions über Rollen
 * ABAC: Kontext-sensitive Zusatzregeln
 */
class HybridPermissionService
{
    public function __construct(
        private readonly PermissionService $rbacService,
        private readonly AbacService $abacService
    ) {
    }

    /**
     * Hybrid Permission Check: RBAC + ABAC
     * 
     * @param User $user
     * @param string $permission
     * @param array $context Kontext-Attribute für ABAC
     * @return bool
     */
    public function hasPermission(User $user, string $permission, array $context = []): bool
    {
        // 1. RBAC Check - Basis-Berechtigung prüfen
        $hasRolePermission = $this->rbacService->hasPermission($user, $permission);
        
        if (!$hasRolePermission) {
            return false; // Keine Basis-Berechtigung -> Zugriff verweigert
        }
        
        // 2. Admin-Überschreibung (Skip ABAC für Admins)
        if ($this->isAdmin($user)) {
            return true; // Admins haben immer Zugriff
        }
        
        // 3. ABAC Check - Kontext-sensitive Regeln
        return $this->abacService->checkContextRules($user, $permission, $context);
    }
    
    /**
     * Erweiterte Permission-Checks mit verschiedenen Strategien
     */
    public function canAccessResource(User $user, string $permission, $resource = null): bool
    {
        $context = [];
        
        // Kontext aus Resource ableiten
        if ($resource) {
            $context = $this->buildContextFromResource($resource);
        }
        
        return $this->hasPermission($user, $permission, $context);
    }
    
    /**
     * Batch-Check für UI-Optimierung
     */
    public function checkMultiplePermissions(User $user, array $permissions, array $context = []): array
    {
        $results = [];
        
        foreach ($permissions as $permission) {
            $results[$permission] = $this->hasPermission($user, $permission, $context);
        }
        
        return $results;
    }
    
    private function isAdmin(User $user): bool
    {
        return $this->rbacService->hasRole($user, 'ROLE_ADMIN');
    }
    
    private function buildContextFromResource($resource): array
    {
        $context = [];
        
        // Standard-Attribute extrahieren
        if (method_exists($resource, 'getCreatedBy')) {
            $context['resource.owner_id'] = $resource->getCreatedBy()?->getId()?->value();
        }
        
        if (method_exists($resource, 'getDepartment')) {
            $context['resource.department'] = $resource->getDepartment();
        }
        
        if (method_exists($resource, 'getStatus')) {
            $context['resource.status'] = $resource->getStatus();
        }
        
        // Zeitkontext
        $context['current.time'] = new \DateTimeImmutable();
        $context['current.day_of_week'] = (int) date('N');
        $context['current.hour'] = (int) date('H');
        
        return $context;
    }
} 