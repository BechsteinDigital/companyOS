<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\DTO;

final class LoginRequest
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly array $scopes = []
    ) {
    }
} 