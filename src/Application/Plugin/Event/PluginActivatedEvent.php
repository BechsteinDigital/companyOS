<?php

declare(strict_types=1);

namespace CompanyOS\Domain\Plugin\Application\Event;

use CompanyOS\Domain\ValueObject\Uuid;

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