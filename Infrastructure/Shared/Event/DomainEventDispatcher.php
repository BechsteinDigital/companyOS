<?php

namespace CompanyOS\Infrastructure\Event;

use CompanyOS\Domain\Shared\Event\DomainEvent;
use CompanyOS\Domain\Shared\Event\EventStore;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class DomainEventDispatcher
{
    public function __construct(
        private EventStore $eventStore,
        private EventDispatcherInterface $eventDispatcher,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Dispatch domain events to Symfony EventDispatcher and store them
     */
    public function dispatch(DomainEvent ...$events): void
    {
        if (empty($events)) {
            return;
        }

        try {
            // Store events in EventStore
            $this->eventStore->store(...$events);

            // Dispatch each event to Symfony EventDispatcher
            foreach ($events as $event) {
                $this->dispatchToSymfony($event);
            }

            $this->logger->info('Domain events dispatched', [
                'count' => count($events),
                'events' => array_map(fn($e) => $e->getEventName(), $events)
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Failed to dispatch domain events', [
                'error' => $e->getMessage(),
                'events' => array_map(fn($e) => $e->getEventName(), $events)
            ]);
            throw $e;
        }
    }

    /**
     * Dispatch a single domain event to Symfony EventDispatcher
     */
    private function dispatchToSymfony(DomainEvent $event): void
    {
        // Create a Symfony event that wraps the domain event
        $symfonyEvent = new DomainEventOccurred($event);
        
        // Dispatch to Symfony EventDispatcher
        $this->eventDispatcher->dispatch($symfonyEvent, $event->getEventName());
        
        // Also dispatch with a generic event name for general listeners
        $this->eventDispatcher->dispatch($symfonyEvent, 'domain.event.occurred');
    }

    /**
     * Get events by name for webhook processing
     */
    public function getEventsByName(string $eventName): array
    {
        return $this->eventStore->getEventsByName($eventName);
    }

    /**
     * Get events by name since a specific date for webhook processing
     */
    public function getEventsByNameSince(string $eventName, \DateTimeImmutable $since): array
    {
        return $this->eventStore->getEventsByNameSince($eventName, $since);
    }

    /**
     * Serialize event for webhook payload
     */
    public function serializeEvent(DomainEvent $event): array
    {
        return [
            'event' => $event->toArray(),
            'webhook' => [
                'timestamp' => (new \DateTimeImmutable())->format('c'),
                'source' => 'companyos',
                'version' => '1.0'
            ]
        ];
    }
} 