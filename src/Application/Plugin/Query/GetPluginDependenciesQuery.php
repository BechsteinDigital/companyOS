<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query;

use CompanyOS\Bundle\CoreBundle\Application\Query\Query;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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