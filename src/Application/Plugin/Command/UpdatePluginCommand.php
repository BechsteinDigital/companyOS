<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Command;

use CompanyOS\Bundle\CoreBundle\Application\Command\Command;

class UpdatePluginCommand implements Command
{
    public function __construct(
        public readonly string $pluginId,
        public readonly string $newVersion,
        public readonly string $updateFilePath,
        public readonly ?array $changelog = null
    ) {
    }
} 