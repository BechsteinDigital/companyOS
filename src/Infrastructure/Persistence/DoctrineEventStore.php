<?php

namespace CompanyOS\Infrastructure\Persistence;

use CompanyOS\Domain\Event\DomainEvent;
use CompanyOS\Domain\Event\EventStore;
use CompanyOS\Domain\Event\StoredEvent;
use CompanyOS\Domain\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineEventStore implements EventStore
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function store(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $storedEvent = StoredEvent::fromDomainEvent($event);
            $this->entityManager->persist($storedEvent);
        }
        
        $this->entityManager->flush();
    }

    public function getEventsForAggregate(Uuid $aggregateId): array
    {
        $storedEvents = $this->entityManager->getRepository(StoredEvent::class)
            ->findBy(['aggregateId' => $aggregateId], ['occurredOn' => 'ASC']);

        return array_map(fn($storedEvent) => $this->reconstructEvent($storedEvent), $storedEvents);
    }

    public function getEventsSince(Uuid $eventId): array
    {
        $event = $this->entityManager->getRepository(StoredEvent::class)->find($eventId);
        if (!$event) {
            return [];
        }

        $storedEvents = $this->entityManager->getRepository(StoredEvent::class)
            ->createQueryBuilder('e')
            ->where('e.occurredOn > :occurredOn')
            ->setParameter('occurredOn', $event->getOccurredOn())
            ->orderBy('e.occurredOn', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($storedEvent) => $this->reconstructEvent($storedEvent), $storedEvents);
    }

    public function getAllEvents(): array
    {
        $storedEvents = $this->entityManager->getRepository(StoredEvent::class)
            ->findBy([], ['occurredOn' => 'ASC']);

        return array_map(fn($storedEvent) => $this->reconstructEvent($storedEvent), $storedEvents);
    }

    public function getEventsByName(string $eventName): array
    {
        $storedEvents = $this->entityManager->getRepository(StoredEvent::class)
            ->findBy(['eventName' => $eventName], ['occurredOn' => 'ASC']);

        return array_map(fn($storedEvent) => $this->reconstructEvent($storedEvent), $storedEvents);
    }

    public function getEventsByNameSince(string $eventName, \DateTimeImmutable $since): array
    {
        $storedEvents = $this->entityManager->getRepository(StoredEvent::class)
            ->createQueryBuilder('e')
            ->where('e.eventName = :eventName')
            ->andWhere('e.occurredOn >= :since')
            ->setParameter('eventName', $eventName)
            ->setParameter('since', $since)
            ->orderBy('e.occurredOn', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(fn($storedEvent) => $this->reconstructEvent($storedEvent), $storedEvents);
    }

    private function reconstructEvent(StoredEvent $storedEvent): DomainEvent
    {
        // For now, we'll return a simple event reconstruction
        // In a full implementation, you'd want to use event factories or reflection
        // to properly reconstruct the original event class
        
        $eventData = $storedEvent->getEventData();
        $aggregateId = Uuid::fromString($eventData['aggregateId'] ?? (string)$storedEvent->getAggregateId());
        
        // Create a generic event for now
        return new class($aggregateId) extends DomainEvent {
            public function getEventName(): string
            {
                return 'reconstructed.event';
            }

            public function getEventVersion(): string
            {
                return '1.0';
            }

            protected function getEventData(): array
            {
                return [];
            }
        };
    }
} 