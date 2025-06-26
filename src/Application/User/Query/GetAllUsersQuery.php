<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class GetAllUsersQuery implements Query
{
    public function __construct(
        public readonly bool $activeOnly = false
    ) {
    }
} 