<?php

namespace CompanyOS\Domain\Plugin\Domain\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class PluginUpdated extends DomainEvent
{
    public function __construct(
        private Uuid $pluginId,
        private string $pluginName,
        private string $oldVersion,
        private string $newVersion,
        private ?array $changelog = null
    ) {
        parent::__construct($pluginId);
    }

    public function getEventName(): string
    {
        return 'plugin.updated';
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getOldVersion(): string
    {
        return $this->oldVersion;
    }

    public function getNewVersion(): string
    {
        return $this->newVersion;
    }

    public function getChangelog(): ?array
    {
        return $this->changelog;
    }

    public function toArray(): array
    {
        return [
            'plugin_id' => (string)$this->pluginId,
            'plugin_name' => $this->pluginName,
            'old_version' => $this->oldVersion,
            'new_version' => $this->newVersion,
            'changelog' => $this->changelog,
            'occurred_at' => $this->occurredAt->format('c')
        ];
    }
} 