<?php

namespace CompanyOS\Application\Plugin\Command;

use CompanyOS\Application\Command\Command;

class ActivatePluginCommand implements Command
{
    public function __construct(
        public readonly string $pluginId
    ) {
    }
} 