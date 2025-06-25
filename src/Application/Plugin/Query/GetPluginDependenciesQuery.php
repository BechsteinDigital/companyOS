<?php

declare(strict_types=1);

namespace CompanyOS\Application\Plugin\Query;

use CompanyOS\Application\Query\Query;
use CompanyOS\Domain\ValueObject\Uuid;

final class GetPluginDependenciesQuery implements Query
{
    public function __construct(
        private readonly Uuid $pluginId
    ) {
    }

    public function getPluginId(): Uuid
    {
        return $this->pluginId;
    }
} 