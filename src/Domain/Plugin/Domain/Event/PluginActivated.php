<?php

namespace CompanyOS\Domain\Plugin\Domain\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use CompanyOS\Domain\Shared\ValueObject\Uuid;

class PluginActivated extends DomainEvent
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
        return 'plugin.activated';
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
            'activatedAt' => $this->getOccurredAt()->format('c')
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