<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class DeletePluginCommand implements Command
{
    public function __construct(
        public readonly string $pluginId
    ) {
    }
} 