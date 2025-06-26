<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class GetAllPluginsQuery implements Query
{
    public function __construct(
        public readonly bool $activeOnly = false
    ) {
    }
} 