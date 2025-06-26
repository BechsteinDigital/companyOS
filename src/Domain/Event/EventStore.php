<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

interface EventStore
{
    /**
     * Store domain events
     */
    public function store(DomainEvent ...$events): void;

    /**
     * Get events for an aggregate
     */
    public function getEventsForAggregate(Uuid $aggregateId): array;

    /**
     * Get all events since a specific event
     */
    public function getEventsSince(Uuid $eventId): array;

    /**
     * Get all events
     */
    public function getAllEvents(): array;

    /**
     * Get events by name (for webhook filtering)
     */
    public function getEventsByName(string $eventName): array;

    /**
     * Get events by name since a specific date (for webhook processing)
     */
    public function getEventsByNameSince(string $eventName, \DateTimeImmutable $since): array;
} 