<?php

namespace CompanyOS\Domain\Plugin\Application\Query;

use CompanyOS\Application\Query\Query;

class GetAllPluginsQuery implements Query
{
    public function __construct(
        public readonly bool $activeOnly = false
    ) {
    }
} 