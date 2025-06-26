<?php

namespace CompanyOS\Application\Plugin\Controller;

use CompanyOS\Application\Plugin\Command\InstallPluginCommand;
use CompanyOS\Application\Plugin\Command\ActivatePluginCommand;
use CompanyOS\Application\Plugin\Command\DeactivatePluginCommand;
use CompanyOS\Application\Plugin\Command\DeletePluginCommand;
use CompanyOS\Application\Plugin\Command\UpdatePluginCommand;
use CompanyOS\Application\Plugin\Query\GetPluginQuery;
use CompanyOS\Application\Plugin\Query\GetAllPluginsQuery;
use CompanyOS\Application\Plugin\DTO\InstallPluginRequest;
use CompanyOS\Application\Plugin\DTO\UpdatePluginRequest;
use CompanyOS\Application\Plugin\DTO\PluginResponse;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(
    name: 'Plugins',
    description: 'Plugin management operations'
)]
class PluginController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private PluginManager $pluginManager
    ) {
    }

    #[OA\Get(
        path: '/api/plugins',
        summary: 'Get all plugins',
        description: 'Retrieve a list of all installed plugins in the system',
        tags: ['Plugins'],
        parameters: [
            new OA\Parameter(
                name: 'active_only',
                description: 'Filter to show only active plugins',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean', example: false)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of plugins retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: PluginResponse::class))
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $activeOnly = $request->query->getBoolean('active_only', false);

        $query = new GetAllPluginsQuery($activeOnly);
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);
        $plugins = $handled ? $handled->getResult() : [];

        return $this->json([
            'success' => true,
            'data' => $plugins
        ]);
    }

    #[OA\Get(
        path: '/api/plugins/{id}',
        summary: 'Get plugin by ID',
        description: 'Retrieve a specific plugin by its unique identifier',
        tags: ['Plugins'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Plugin\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Plugin retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: new Model(type: PluginResponse::class))
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Plugin not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $query = new GetPluginQuery($id);
        $plugin = $this->queryBus->dispatch($query);

        if ($plugin === null) {
            return $this->json([
                'success' => false,
                'message' => 'Plugin not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $plugin
        ]);
    }

    #[OA\Get(
        path: '/api/plugins/loaded',
        summary: 'Get loaded plugins',
        description: 'Retrieve a list of all loaded plugins with their instances',
        tags: ['Plugins'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Loaded plugins retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        )
                    ]
                )
            )
        ]
    )]
    #[Route('/loaded', methods: ['GET'])]
    public function getLoadedPlugins(): JsonResponse
    {
        $loadedPlugins = $this->pluginManager->getLoadedPlugins();
        
        $plugins = [];
        foreach ($loadedPlugins as $name => $plugin) {
            $plugins[] = [
                'name' => $plugin->getName(),
                'version' => $plugin->getVersion(),
                'author' => $plugin->getAuthor(),
                'description' => $plugin->getDescription(),
                'label' => $plugin->getLabel(),
                'manufacturer' => $plugin->getManufacturer(),
                'compatible' => $plugin->isCompatible(),
                'configuration' => $plugin->getConfiguration(),
                'assets' => $plugin->getAssets()
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $plugins
        ]);
    }

    #[OA\Post(
        path: '/api/plugins',
        summary: 'Install a new plugin',
        description: 'Install a new plugin into the system',
        tags: ['Plugins'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: InstallPluginRequest::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Plugin installed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin installed successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/PluginResponse')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request - validation failed',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Conflict - plugin with this name already exists',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin with this name already exists')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function install(Request $request): JsonResponse
    {
        try {
            /** @var InstallPluginRequest $dto */
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                InstallPluginRequest::class,
                'json'
            );

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                return $this->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new InstallPluginCommand(
                $dto->name,
                $dto->version,
                $dto->author,
                $dto->meta
            );

            $plugin = $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Plugin installed successfully',
                'data' => $plugin
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Post(
        path: '/api/plugins/{id}/activate',
        summary: 'Activate a plugin',
        description: 'Activate a previously installed plugin',
        tags: ['Plugins'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Plugin\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Plugin activated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin activated successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Plugin not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('/{id}/activate', methods: ['POST'])]
    public function activate(string $id): JsonResponse
    {
        try {
            $command = new ActivatePluginCommand($id);
            $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Plugin activated successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Post(
        path: '/api/plugins/{id}/deactivate',
        summary: 'Deactivate a plugin',
        description: 'Deactivate an active plugin',
        tags: ['Plugins'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Plugin\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Plugin deactivated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin deactivated successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Plugin not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('/{id}/deactivate', methods: ['POST'])]
    public function deactivate(string $id): JsonResponse
    {
        try {
            $command = new DeactivatePluginCommand($id);
            $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Plugin deactivated successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: '/api/plugins/{id}',
        summary: 'Delete a plugin',
        description: 'Permanently delete a plugin from the system',
        tags: ['Plugins'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Plugin\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Plugin deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Plugin not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Plugin not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Internal server error')
                    ]
                )
            )
        ]
    )]
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        try {
            $command = new DeletePluginCommand($id);
            $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Plugin deleted successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/update', methods: ['POST'])]
    #[OA\Post(
        summary: 'Update plugin',
        description: 'Upload and install a new version of a plugin',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'plugin_file',
                            type: 'string',
                            format: 'binary',
                            description: 'Plugin archive file (.tar.gz)'
                        ),
                        new OA\Property(
                            property: 'version',
                            type: 'string',
                            description: 'New plugin version'
                        ),
                        new OA\Property(
                            property: 'changelog',
                            type: 'string',
                            description: 'Changelog for this update'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Plugin updated successfully'
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 404, description: 'Plugin not found')
        ]
    )]
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            $uploadedFile = $request->files->get('plugin_file');
            $version = $request->request->get('version');
            $changelog = $request->request->get('changelog');

            if (!$uploadedFile || !$version) {
                return $this->json([
                    'success' => false,
                    'message' => 'Plugin file and version are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate file type
            if ($uploadedFile->getClientMimeType() !== 'application/gzip' && 
                $uploadedFile->getClientOriginalExtension() !== 'tar.gz') {
                return $this->json([
                    'success' => false,
                    'message' => 'Only .tar.gz files are allowed'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Move uploaded file to temporary location
            $tempPath = $uploadedFile->move(
                sys_get_temp_dir(),
                'plugin_update_' . uniqid() . '.tar.gz'
            )->getPathname();

            $command = new UpdatePluginCommand(
                $id,
                $version,
                $tempPath,
                $changelog ? json_decode($changelog, true) : null
            );

            $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Plugin updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
} 