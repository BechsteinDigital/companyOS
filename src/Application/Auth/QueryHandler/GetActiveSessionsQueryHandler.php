<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Query\GetActiveSessionsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Auth\DTO\ActiveSessionResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

final class GetActiveSessionsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly AccessTokenRepositoryInterface $accessTokenRepository
    ) {
    }

    public function __invoke(GetActiveSessionsQuery $query): array
    {
        $accessTokens = $this->accessTokenRepository->findActiveByUserId($query->getUserId());
        
        return array_map(
            fn($token) => new ActiveSessionResponse(
                id: $token->getId()->value(),
                clientId: $token->getClientId()->value(),
                scopes: $token->getScopes(),
                expiresAt: $token->getExpiresAt()->format('Y-m-d H:i:s'),
                createdAt: $token->getCreatedAt()->format('Y-m-d H:i:s')
            ),
            $accessTokens
        );
    }
} 