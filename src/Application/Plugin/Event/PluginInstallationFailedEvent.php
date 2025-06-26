<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Event;

final class PluginInstallationFailedEvent
{
    public function __construct(
        public readonly string $pluginName,
        public readonly string $version,
        public readonly string $reason,
        public readonly array $errors,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }
} 