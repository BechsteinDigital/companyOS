<?php

namespace CompanyOS\Domain\Plugin\Domain\Event;

use CompanyOS\Domain\Event\DomainEvent;
use CompanyOS\Domain\ValueObject\Uuid;

class PluginDeactivated extends DomainEvent
{
    public function __construct(
        Uuid $aggregateId,
        private string $pluginName,
        private string $version
    ) {
        parent::__construct($aggregateId);
    }

    public function getEventName(): string
    {
        return 'plugin.deactivated';
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }

    protected function getEventData(): array
    {
        return [
            'pluginName' => $this->pluginName,
            'version' => $this->version,
            'deactivatedAt' => $this->getOccurredAt()->format('c')
        ];
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
} 