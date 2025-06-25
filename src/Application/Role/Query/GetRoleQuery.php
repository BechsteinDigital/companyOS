<?php

namespace CompanyOS\Application\Role\Query;

class GetRoleQuery
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 