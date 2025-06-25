<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\DTO;

final class TokenValidationResponse
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $userId = null,
        public readonly ?array $scopes = null,
        public readonly ?string $expiresAt = null
    ) {
    }
} 