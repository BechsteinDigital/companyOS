<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Query;

class GetAllRolesQuery
{
    public function __construct(
        public readonly bool $includeSystem = true,
        public readonly ?string $search = null
    ) {
    }
} 