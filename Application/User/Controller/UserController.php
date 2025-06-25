<?php

namespace CompanyOS\Domain\User\Application\Controller;

use CompanyOS\Domain\User\Application\Command\CreateUserCommand;
use CompanyOS\Domain\User\Application\Command\UpdateUserCommand;
use CompanyOS\Domain\User\Application\Command\DeleteUserCommand;
use CompanyOS\Domain\User\Application\DTO\CreateUserRequest;
use CompanyOS\Domain\User\Application\DTO\UpdateUserRequest;
use CompanyOS\Domain\User\Application\DTO\UserResponse;
use CompanyOS\Domain\User\Application\Query\GetUserQuery;
use CompanyOS\Domain\User\Application\Query\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Messenger\Stamp\HandledStamp;


#[OA\Tag(name: 'Users', description: 'User management operations')]
class UserController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private SerializerInterface $serializer
    ) {}

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get all users',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: UserResponse::class))
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $activeOnly = $request->query->getBoolean('active_only', false);
        $query = new GetAllUsersQuery($activeOnly);
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);
        $users = $handled ? $handled->getResult() : [];

        return $this->json([
            'success' => true,
            'data' => $users
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get user by ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User details',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class))
            ),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $query = new GetUserQuery($id);
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);
        $user = $handled ? $handled->getResult() : null;

        if ($user === null) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return $this->json(['success' => true, 'data' => $user]);
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateUserRequest::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class))
            ),
            new OA\Response(response: 400, description: 'Bad request')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            /** @var CreateUserRequest $dto */
            $dto = $this->serializer->deserialize($request->getContent(), CreateUserRequest::class, 'json');

            $command = new CreateUserCommand(
                $dto->email,
                $dto->firstName,
                $dto->lastName,
                $dto->password,
                $dto->roleIds
            );

            $user = $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: UpdateUserRequest::class))
        ),
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User updated',
                content: new OA\JsonContent(ref: new Model(type: UserResponse::class))
            )
        ]
    )]
    public function update(string $id, Request $request): JsonResponse
    {
        try {
            /** @var UpdateUserRequest $dto */
            $dto = $this->serializer->deserialize($request->getContent(), UpdateUserRequest::class, 'json');

            $command = new UpdateUserCommand(
                $id,
                $dto->email,
                $dto->firstName,
                $dto->lastName,
                $dto->roleIds
            );

            $user = $this->commandBus->dispatch($command);

            return $this->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete user',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User deleted',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'User deleted successfully')
                    ]
                )
            )
        ]
    )]
    public function delete(string $id): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new DeleteUserCommand($id));

            return $this->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
