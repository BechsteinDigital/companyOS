<?php

namespace CompanyOS\Infrastructure\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use Symfony\Contracts\EventDispatcher\Event;

class DomainEventOccurred extends Event
{
    public function __construct(
        private DomainEvent $domainEvent
    ) {
    }

    public function getDomainEvent(): DomainEvent
    {
        return $this->domainEvent;
    }

    public function getEventName(): string
    {
        return $this->domainEvent->getEventName();
    }

    public function getEventId(): string
    {
        return $this->domainEvent->getEventId();
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->domainEvent->getOccurredAt();
    }

    public function toArray(): array
    {
        return $this->domainEvent->toArray();
    }
} 