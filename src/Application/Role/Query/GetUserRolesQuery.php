<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Query;

class GetUserRolesQuery
{
    public function __construct(
        public readonly string $userId
    ) {
    }
} 