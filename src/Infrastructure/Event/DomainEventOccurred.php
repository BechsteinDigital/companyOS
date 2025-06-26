<?php

namespace CompanyOS\Bundle\CoreBundle\Infrastructure\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;
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