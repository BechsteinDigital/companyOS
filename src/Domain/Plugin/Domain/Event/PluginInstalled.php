<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

class PluginInstalled extends DomainEvent
{
    public function __construct(
        Uuid $aggregateId,
        private string $pluginName,
        private string $version,
        private string $author
    ) {
        parent::__construct($aggregateId);
    }

    public function getEventName(): string
    {
        return 'plugin.installed';
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
            'author' => $this->author,
            'installedAt' => $this->getOccurredAt()->format('c')
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

    public function getAuthor(): string
    {
        return $this->author;
    }
} 