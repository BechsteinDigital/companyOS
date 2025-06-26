<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

class GetPluginQuery implements Query
{
    public function __construct(
        public readonly string $pluginId
    ) {
    }
} 