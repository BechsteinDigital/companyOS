<?php

declare(strict_types=1);

namespace CompanyOS\Application\Auth\DTO;

final class ActiveSessionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $clientId,
        public readonly array $scopes,
        public readonly string $expiresAt,
        public readonly string $createdAt
    ) {
    }
} 