<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;

final class CheckPluginCompatibilityQuery implements Query
{
    public function __construct(
        private readonly string $pluginName,
        private readonly string $version,
        private readonly array $systemRequirements
    ) {
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getSystemRequirements(): array
    {
        return $this->systemRequirements;
    }
} 