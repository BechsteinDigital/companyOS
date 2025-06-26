<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Role\Query;

class GetRoleQuery
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 