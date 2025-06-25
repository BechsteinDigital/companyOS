<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\QueryHandler;

use CompanyOS\Domain\Auth\Application\Query\GetOAuthClientsQuery;
use CompanyOS\Domain\Auth\Application\DTO\OAuthClientResponse;
use CompanyOS\Domain\Auth\Domain\Repository\ClientRepositoryInterface;
use CompanyOS\Application\Query\QueryHandlerInterface;

final class GetOAuthClientsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {
    }

    public function __invoke(GetOAuthClientsQuery $query): array
    {
        $clients = $this->clientRepository->findAll(
            clientId: $query->getClientId(),
            clientName: $query->getClientName()
        );
        
        return array_map(
            fn($client) => new OAuthClientResponse(
                id: $client->getId()->value(),
                clientId: $client->getClientId()->value(),
                clientName: $client->getClientName()->value(),
                redirectUris: $client->getRedirectUris(),
                scopes: $client->getScopes(),
                isActive: $client->isActive(),
                createdAt: $client->getCreatedAt()->format('Y-m-d H:i:s')
            ),
            $clients
        );
    }
} 