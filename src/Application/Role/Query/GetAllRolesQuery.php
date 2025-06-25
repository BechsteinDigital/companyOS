<?php

namespace CompanyOS\Domain\Role\Application\Query;

class GetAllRolesQuery
{
    public function __construct(
        public readonly bool $includeSystem = true,
        public readonly ?string $search = null
    ) {
    }
} 