<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\DTO;

final class RefreshTokenRequest
{
    public function __construct(
        public readonly string $refreshToken,
        public readonly string $clientId,
        public readonly string $clientSecret
    ) {
    }
} 