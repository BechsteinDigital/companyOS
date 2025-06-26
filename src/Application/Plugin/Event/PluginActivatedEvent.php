<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Event;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

final class PluginActivatedEvent
{
    public function __construct(
        public readonly Uuid $pluginId,
        public readonly string $pluginName,
        public readonly string $version,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 