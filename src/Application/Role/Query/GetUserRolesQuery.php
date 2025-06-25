<?php

namespace CompanyOS\Domain\Role\Application\Query;

class GetUserRolesQuery
{
    public function __construct(
        public readonly string $userId
    ) {
    }
} 