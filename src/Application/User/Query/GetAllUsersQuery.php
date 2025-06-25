<?php

namespace CompanyOS\Domain\User\Application\Query;

use CompanyOS\Application\Query\Query;

class GetAllUsersQuery implements Query
{
    public function __construct(
        public readonly bool $activeOnly = false
    ) {
    }
} 