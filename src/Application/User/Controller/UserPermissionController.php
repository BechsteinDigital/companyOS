<?php

namespace CompanyOS\Application\User\Controller;

use CompanyOS\Application\Role\Service\PermissionService;
use CompanyOS\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'User Permissions', description: 'User permission management and checks')]
#[Route('/user-permissions')]
class UserPermissionController extends AbstractController
{
    public function __construct(
        private PermissionService $permissionService,
        private UserRepositoryInterface $userRepository
    ) {}

    #[Route('/check/{userId}/{permission}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Check if user has specific permission',
        parameters: [
            new OA\Parameter(
                name: 'userId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'permission',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'user.create')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permission check result',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'hasPermission', type: 'boolean'),
                        new OA\Property(property: 'permission', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function checkPermission(string $userId, string $permission): JsonResponse
    {
        $user = $this->userRepository->findById(Uuid::fromString($userId));
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $hasPermission = $this->permissionService->hasPermission($user, $permission);

        return $this->json([
            'success' => true,
            'hasPermission' => $hasPermission,
            'permission' => $permission
        ]);
    }

    #[Route('/list/{userId}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all permissions for a user',
        parameters: [
            new OA\Parameter(
                name: 'userId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User permissions list',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            )
        ]
    )]
    public function getUserPermissions(string $userId): JsonResponse
    {
        $user = $this->userRepository->findById(Uuid::fromString($userId));
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $permissions = $this->permissionService->getUserPermissions($user);

        return $this->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    #[Route('/protected-resource', methods: ['GET'])]
    #[OA\Get(
        summary: 'Protected resource that requires specific permission',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Access granted',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Access denied')
        ]
    )]
    public function protectedResource(): JsonResponse
    {
        // Diese Methode demonstriert die Verwendung von @IsGranted
        // In einer echten Anwendung würden Sie @IsGranted("PERMISSION_USER_CREATE") verwenden
        
        $user = $this->getUser();
        
        if (!$user) {
            throw new AccessDeniedException('User not authenticated');
        }

        if (!$this->permissionService->hasPermission($user, 'user.create')) {
            throw new AccessDeniedException('Insufficient permissions');
        }

        return $this->json([
            'success' => true,
            'message' => 'Access granted to protected resource'
        ]);
    }
} 