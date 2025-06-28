<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Query\GetOAuthClientsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Auth\DTO\OAuthClientResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\ClientRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

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
                createdAt: $client->getCreatedAt()
            ),
            $clients
        );
    }
} 