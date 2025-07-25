<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\Controller;

use CompanyOS\Bundle\CoreBundle\Application\Webhook\Command\CreateWebhookCommand;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\Command\UpdateWebhookCommand;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\Command\DeleteWebhookCommand;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\Query\GetWebhookQuery;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\Query\GetAllWebhooksQuery;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\DTO\CreateWebhookRequest;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\DTO\WebhookResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[OA\Tag(name: 'Webhooks', description: 'Webhook management operations')]

class WebhookController extends AbstractController
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository,
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'api_webhooks_list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all webhooks',
        description: 'Retrieve a list of all webhooks with optional filtering',
        parameters: [
            new OA\Parameter(
                name: 'active_only',
                description: 'Show only active webhooks',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean', example: true)
            ),
            new OA\Parameter(
                name: 'event_type',
                description: 'Filter by event type',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'user.created')
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search webhooks by name or URL',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'slack')
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of webhooks to return',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 50)
            ),
            new OA\Parameter(
                name: 'offset',
                description: 'Number of webhooks to skip',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 0)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of webhooks retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: WebhookResponse::class))
                        )
                    ]
                )
            )
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        $activeOnly = $request->query->getBoolean('active_only', false);
        $eventType = $request->query->get('event_type');
        $search = $request->query->get('search');
        $limit = (int) $request->query->get('limit', 50);
        $offset = (int) $request->query->get('offset', 0);

        $query = new GetAllWebhooksQuery($activeOnly, $eventType, $search, $limit, $offset);
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);
        $webhooks = $handled ? $handled->getResult() : [];

        return $this->json([
            'success' => true,
            'data' => $webhooks
        ]);
    }

    #[Route('/{id}', name: 'api_webhooks_show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get webhook by ID',
        description: 'Retrieve a specific webhook by its unique identifier',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Webhook\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Webhook retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', ref: new Model(type: WebhookResponse::class))
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Webhook not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook not found')
                    ]
                )
            )
        ]
    )]
    public function getOne(string $id): JsonResponse
    {
        $query = new GetWebhookQuery($id);
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);
        $webhook = $handled ? $handled->getResult() : null;

        if ($webhook === null) {
            return $this->json([
                'success' => false,
                'message' => 'Webhook not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $webhook
        ]);
    }

    #[Route('', name: 'api_webhooks_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create a new webhook',
        description: 'Create a new webhook in the system',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateWebhookRequest::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Webhook created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook created successfully'),
                        new OA\Property(property: 'data', ref: new Model(type: WebhookResponse::class))
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
            )
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $command = new CreateWebhookCommand(
                $data['name'],
                $data['url'],
                $data['events'] ?? [],
                $data['secret'] ?? null,
                $data['isActive'] ?? true
            );

            $webhook = $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Webhook created successfully',
                'data' => $webhook
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_webhooks_update', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update a webhook',
        description: 'Update an existing webhook in the system',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Webhook\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateWebhookRequest::class))
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Webhook updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook updated successfully'),
                        new OA\Property(property: 'data', ref: new Model(type: WebhookResponse::class))
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Webhook not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook not found')
                    ]
                )
            )
        ]
    )]
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $command = new UpdateWebhookCommand(
                $id,
                $data['name'],
                $data['url'],
                $data['events'] ?? [],
                $data['secret'] ?? null,
                $data['isActive'] ?? true
            );

            $webhook = $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'Webhook updated successfully',
                'data' => $webhook
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'api_webhooks_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a webhook',
        description: 'Delete a webhook from the system',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Webhook\'s unique identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Webhook deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Webhook not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Webhook not found')
                    ]
                )
            )
        ]
    )]
    public function delete(string $id): JsonResponse
    {
        $uuid = Uuid::fromString($id);
        $command = new DeleteWebhookCommand($uuid);
        $this->commandBus->dispatch($command);
        return $this->json([
            'success' => true,
            'message' => 'Webhook deleted successfully'
        ]);
    }
} 