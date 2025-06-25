<?php

declare(strict_types=1);

namespace CompanyOS\Application\Plugin\Event;

use CompanyOS\Domain\ValueObject\Uuid;

final class PluginUpdatedEvent
{
    public function __construct(
        public readonly Uuid $pluginId,
        public readonly string $pluginName,
        public readonly string $oldVersion,
        public readonly string $newVersion,
        public readonly array $changelog,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 