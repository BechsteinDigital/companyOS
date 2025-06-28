<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Auth\Query\ValidateTokenQuery;
use CompanyOS\Bundle\CoreBundle\Application\Auth\DTO\TokenValidationResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

final class ValidateTokenQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly AccessTokenRepositoryInterface $accessTokenRepository
    ) {
    }

    public function __invoke(ValidateTokenQuery $query): TokenValidationResponse
    {
        $accessToken = $this->accessTokenRepository->findByToken($query->getAccessToken());
        
        if (!$accessToken) {
            return new TokenValidationResponse(
                isValid: false,
                userId: null,
                clientId: null,
                scopes: [],
                expiresAt: null
            );
        }

        if ($accessToken->isExpired()) {
            return new TokenValidationResponse(
                isValid: false,
                userId: $accessToken->getUserId()->value(),
                clientId: $accessToken->getClientId()->value(),
                scopes: $accessToken->getScopes(),
                expiresAt: $accessToken->getExpiresAt()
            );
        }

        return new TokenValidationResponse(
            isValid: true,
            userId: $accessToken->getUserId()->value(),
            clientId: $accessToken->getClientId()->value(),
            scopes: $accessToken->getScopes(),
            expiresAt: $accessToken->getExpiresAt()
        );
    }
} 