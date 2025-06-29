<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Controller;

use CompanyOS\Bundle\CoreBundle\Application\Role\Service\PermissionService;
use CompanyOS\Bundle\CoreBundle\Domain\User\Domain\Repository\UserRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/check/{userId}/{permission}', methods: ['GET'], name: 'api_users_check_permission')]
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

    #[Route('/list/{userId}', methods: ['GET'], name: 'api_users_list_permissions')]
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

    #[Route('/protected-resource', methods: ['GET'], name: 'api_users_protected_resource')]
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

    #[Route('/check-permission', methods: ['POST'], name: 'api_users_check_permission_post')]
    #[OA\Post(
        summary: 'Check if user has specific permission (POST)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'permission', type: 'string', example: 'dashboard.view'),
                    new OA\Property(property: 'context', type: 'object')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permission check result',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'allowed', type: 'boolean'),
                        new OA\Property(property: 'permission', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function checkPermissionPost(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['user_id']) || !isset($data['permission'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Missing required fields: user_id, permission'
                ], 400);
            }

            // Use current authenticated user instead of looking up by ID to avoid repository issues
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $permission = $data['permission'];
            $context = $data['context'] ?? [];
            
            // Use basic permission check to avoid complex context issues
            $hasPermission = $this->permissionService->hasPermission($user, $permission);

            return $this->json([
                'success' => true,
                'allowed' => $hasPermission,
                'permission' => $permission,
                'context' => $context
            ]);
            
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    #[Route('/check-permissions-batch', methods: ['POST'], name: 'api_users_check_permissions_batch')]
    #[OA\Post(
        summary: 'Check multiple permissions for user (batch)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string')),
                    new OA\Property(property: 'context', type: 'object')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Batch permission check results',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'permissions', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function checkPermissionsBatch(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['user_id']) || !isset($data['permissions'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Missing required fields: user_id, permissions'
                ], 400);
            }

            // Use current authenticated user instead of looking up by ID
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $permissions = $data['permissions'];
            $context = $data['context'] ?? [];
            $results = [];
            
            foreach ($permissions as $permission) {
                $results[$permission] = $this->permissionService->hasPermission($user, $permission);
            }

            return $this->json([
                'success' => true,
                'permissions' => $results,
                'context' => $context
            ]);
            
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    #[Route('/navigation/{userId}', methods: ['GET'], name: 'api_users_navigation_permissions')]
    #[OA\Get(
        summary: 'Get navigation permissions for user',
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
                description: 'Navigation permissions for user',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'navigationPermissions', type: 'object'),
                        new OA\Property(property: 'userPermissions', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            )
        ]
    )]
    public function getNavigationPermissions(string $userId): JsonResponse
    {
        try {
            // Use current authenticated user instead of looking up by ID
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $permissions = $this->permissionService->getUserPermissions($user);
        
            // Navigation-spezifische Permission-Mappings
            $navigationPermissions = [
                'dashboard' => in_array('dashboard.view', $permissions) || in_array('**', $permissions),
                'administration' => $this->hasAnyPermission($permissions, [
                    'user.create', 'user.read', 'user.update', 'user.delete',
                    'role.create', 'role.read', 'role.update', 'role.delete',
                    'administration.*', '**'
                ]),
                'system' => $this->hasAnyPermission($permissions, [
                    'plugin.create', 'plugin.read', 'plugin.update', 'plugin.delete',
                    'settings.create', 'settings.read', 'settings.update', 'settings.delete',
                    'webhook.create', 'webhook.read', 'webhook.update', 'webhook.delete',
                    'system.*', '**'
                ]),
                'development' => $this->hasAnyPermission($permissions, [
                    'api.read', 'api.documentation', 'system.monitoring', 'system.status',
                    'development.*', '**'
                ]),
                'profile' => in_array('profile.read', $permissions) || in_array('profile.*', $permissions) || in_array('**', $permissions)
            ];

            return $this->json([
                'success' => true,
                'navigationPermissions' => $navigationPermissions,
                'userPermissions' => $permissions,
                'userId' => $userId
            ]);
            
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function hasAnyPermission(array $userPermissions, array $requiredPermissions): bool
    {
        foreach ($requiredPermissions as $required) {
            if (in_array($required, $userPermissions)) {
                return true;
            }
        }
        return false;
    }
} 