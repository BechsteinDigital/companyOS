<?php

namespace CompanyOS\Bundle\CoreBundle\Application\User\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class GetUserQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 