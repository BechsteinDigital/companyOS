<?php

namespace CompanyOS\Domain\Role\Application\Query;

class GetRoleQuery
{
    public function __construct(
        public readonly string $id
    ) {
    }
} 