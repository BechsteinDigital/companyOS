<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class DeactivatePluginCommand implements Command
{
    public function __construct(
        public readonly string $pluginId
    ) {
    }
} 