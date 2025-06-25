<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Auth\Application\Query;

use CompanyOS\Application\Query\Query;

final class ValidateTokenQuery implements Query
{
    public function __construct(
        private readonly string $accessToken
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
} 