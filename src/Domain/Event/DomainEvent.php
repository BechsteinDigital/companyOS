<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use DateTimeImmutable;

abstract class DomainEvent
{
    private Uuid $eventId;
    private DateTimeImmutable $occurredOn;
    private Uuid $aggregateId;

    public function __construct(Uuid $aggregateId)
    {
        $this->eventId = Uuid::random();
        $this->occurredOn = new DateTimeImmutable();
        $this->aggregateId = $aggregateId;
    }

    public function getEventId(): Uuid
    {
        return $this->eventId;
    }

    public function getOccurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function getAggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    abstract public function getEventName(): string;

    abstract public function getEventVersion(): string;

    /**
     * Convert event to array for serialization
     */
    public function toArray(): array
    {
        return [
            'eventId' => (string)$this->eventId,
            'aggregateId' => (string)$this->aggregateId,
            'eventName' => $this->getEventName(),
            'eventVersion' => $this->getEventVersion(),
            'occurredOn' => $this->occurredOn->format('c'),
            'data' => $this->getEventData()
        ];
    }

    /**
     * Get event-specific data
     */
    protected function getEventData(): array
    {
        return [];
    }
} 