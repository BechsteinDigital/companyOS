<?php

namespace CompanyOS\Domain\User\Application\Query;

use CompanyOS\Application\Query\Query;

class GetUserQuery implements Query
{
    public function __construct(
        public readonly string $userId
    ) {
    }
} 