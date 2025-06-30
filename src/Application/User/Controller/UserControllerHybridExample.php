<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\User\Controller;

use CompanyOS\Bundle\CoreBundle\Application\Role\Service\HybridPermissionService;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Beispiel-Controller für Hybrid Permission System
 */
#[Route('/api/users-hybrid')]
class UserControllerHybridExample extends AbstractController
{
    public function __construct(
        private readonly HybridPermissionService $permissionService,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    #[Route('/{id}', methods: ['PUT'], name: 'api_users_hybrid_update')]
    public function updateUser(string $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $targetUser = $this->userRepository->findById(Uuid::fromString($id));
        
        if (!$targetUser) {
            return $this->json(['error' => 'User not found'], 404);
        }
        
        // Hybrid Permission Check mit Resource-Kontext
        $canUpdate = $this->permissionService->canAccessResource(
            user: $currentUser,
            permission: 'user.update',
            resource: $targetUser
        );
        
        if (!$canUpdate) {
            return $this->json([
                'error' => 'Access denied',
                'reason' => 'RBAC or ABAC rules failed'
            ], 403);
        }
        
        // Update Logic...
        return $this->json(['success' => true]);
    }
    
    #[Route('/{id}', methods: ['DELETE'], name: 'api_users_hybrid_delete')]  
    public function deleteUser(string $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $targetUser = $this->userRepository->findById(Uuid::fromString($id));
        
        // Expliziter Kontext für spezielle Regeln
        $context = [
            'resource.owner_id' => $targetUser->getId()->value(),
            'resource.department' => $targetUser->getDepartment(),
            'action.risk_level' => 'high' // User-Löschung ist kritisch
        ];
        
        $canDelete = $this->permissionService->hasPermission(
            user: $currentUser,
            permission: 'user.delete',
            context: $context
        );
        
        if (!$canDelete) {
            return $this->json([
                'error' => 'Access denied',
                'reason' => 'Time restriction or ownership rule failed'
            ], 403);
        }
        
        // Delete Logic...
        return $this->json(['success' => true]);
    }
    
    #[Route('/batch-permissions/{userId}', methods: ['GET'], name: 'api_users_hybrid_batch_check')]
    public function getBatchPermissions(string $userId): JsonResponse
    {
        $currentUser = $this->getUser();
        $targetUser = $this->userRepository->findById(Uuid::fromString($userId));
        
        $permissions = ['user.read', 'user.update', 'user.delete'];
        $context = [
            'resource.owner_id' => $targetUser->getId()->value(),
            'resource.department' => $targetUser->getDepartment(),
        ];
        
        // Batch-Check für UI-Optimierung
        $results = $this->permissionService->checkMultiplePermissions(
            user: $currentUser,
            permissions: $permissions,
            context: $context
        );
        
        return $this->json([
            'user_id' => $userId,
            'permissions' => $results,
            'context' => $context
        ]);
    }
} 