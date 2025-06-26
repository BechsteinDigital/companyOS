<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Controller;

use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'OAuth2 Clients', description: 'OAuth2 client management')]
class OAuthClientController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'List OAuth2 clients',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of OAuth2 clients',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'identifier', type: 'string'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'isConfidential', type: 'boolean'),
                            new OA\Property(property: 'isActive', type: 'boolean'),
                            new OA\Property(property: 'scopes', type: 'array', items: new OA\Items(type: 'string'))
                        ]
                    )
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();
        
        $data = array_map(function (Client $client) {
            return [
                'id' => $client->getId(),
                'identifier' => $client->getIdentifier(),
                'name' => $client->getName(),
                'isConfidential' => $client->isConfidential(),
                'isActive' => $client->isActive(),
                'scopes' => $client->getScopes(),
                'redirectUris' => $client->getRedirectUris()
            ];
        }, $clients);

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        summary: 'Create OAuth2 client',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'identifier', type: 'string', example: 'frontend-client'),
                    new OA\Property(property: 'name', type: 'string', example: 'Frontend Application'),
                    new OA\Property(property: 'secret', type: 'string', example: 'secret123', nullable: true),
                    new OA\Property(property: 'isConfidential', type: 'boolean', example: false),
                    new OA\Property(property: 'scopes', type: 'array', items: new OA\Items(type: 'string'), example: ['basic', 'user.read']),
                    new OA\Property(property: 'redirectUris', type: 'array', items: new OA\Items(type: 'string'), example: ['http://localhost:3000/callback'])
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Client created',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean'),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = new Client(
            $data['identifier'],
            $data['name']
        );

        if (isset($data['secret'])) {
            $client->setSecret($data['secret']);
        }

        if (isset($data['isConfidential'])) {
            $client->setIsConfidential($data['isConfidential']);
        }

        if (isset($data['scopes'])) {
            $client->setScopes($data['scopes']);
        }

        if (isset($data['redirectUris'])) {
            $client->setRedirectUris($data['redirectUris']);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'OAuth2 client created successfully',
            'data' => [
                'id' => $client->getId(),
                'identifier' => $client->getIdentifier(),
                'name' => $client->getName()
            ]
        ], 201);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get OAuth2 client details',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client details',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 404, description: 'Client not found')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->find($id);

        if (!$client) {
            return $this->json(['success' => false, 'message' => 'Client not found'], 404);
        }

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $client->getId(),
                'identifier' => $client->getIdentifier(),
                'name' => $client->getName(),
                'isConfidential' => $client->isConfidential(),
                'isActive' => $client->isActive(),
                'scopes' => $client->getScopes(),
                'redirectUris' => $client->getRedirectUris(),
                'createdAt' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $client->getUpdatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }
} 