<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\QueryHandler;

use CompanyOS\Application\Auth\Query\ValidateTokenQuery;
use CompanyOS\Application\Auth\DTO\TokenValidationResponse;
use CompanyOS\Domain\Auth\Domain\Repository\AccessTokenRepositoryInterface;
use CompanyOS\Application\Query\QueryHandlerInterface;

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
                expiresAt: $accessToken->getExpiresAt()->format('Y-m-d H:i:s')
            );
        }

        return new TokenValidationResponse(
            isValid: true,
            userId: $accessToken->getUserId()->value(),
            clientId: $accessToken->getClientId()->value(),
            scopes: $accessToken->getScopes(),
            expiresAt: $accessToken->getExpiresAt()->format('Y-m-d H:i:s')
        );
    }
} 