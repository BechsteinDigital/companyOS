<?php

namespace CompanyOS\Domain\Event;

use CompanyOS\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stored_events')]
#[ORM\Index(columns: ['aggregate_id'])]
#[ORM\Index(columns: ['event_name'])]
#[ORM\Index(columns: ['occurred_on'])]
class StoredEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $eventId;

    #[ORM\Column(type: 'uuid')]
    private Uuid $aggregateId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $eventName;

    #[ORM\Column(type: 'string', length: 10)]
    private string $eventVersion;

    #[ORM\Column(type: 'json')]
    private array $eventData;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $occurredOn;

    public function __construct(
        Uuid $eventId,
        Uuid $aggregateId,
        string $eventName,
        string $eventVersion,
        array $eventData,
        \DateTimeImmutable $occurredOn
    ) {
        $this->eventId = $eventId;
        $this->aggregateId = $aggregateId;
        $this->eventName = $eventName;
        $this->eventVersion = $eventVersion;
        $this->eventData = $eventData;
        $this->occurredOn = $occurredOn;
    }

    public function getEventId(): Uuid
    {
        return $this->eventId;
    }

    public function getAggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getEventVersion(): string
    {
        return $this->eventVersion;
    }

    public function getEventData(): array
    {
        return $this->eventData;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function fromDomainEvent(DomainEvent $event): self
    {
        return new self(
            $event->getEventId(),
            $event->getAggregateId(),
            $event->getEventName(),
            $event->getEventVersion(),
            $event->toArray(),
            $event->getOccurredOn()
        );
    }
} 