<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Auth\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

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