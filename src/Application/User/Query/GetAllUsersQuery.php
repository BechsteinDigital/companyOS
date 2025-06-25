<?php

namespace CompanyOS\Application\User\Query;

use CompanyOS\Application\Query\Query;

class GetAllUsersQuery implements Query
{
    public function __construct(
        public readonly bool $activeOnly = false
    ) {
    }
} 