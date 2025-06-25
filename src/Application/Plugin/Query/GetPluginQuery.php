<?php

namespace CompanyOS\Application\Plugin\Query;

use CompanyOS\Application\Query\Query;

class GetPluginQuery implements Query
{
    public function __construct(
        public readonly string $pluginId
    ) {
    }
} 