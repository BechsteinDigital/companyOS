<?php

namespace CompanyOS\Infrastructure\Event;

use CompanyOS\Infrastructure\Event\DomainEventOccurred;
use CompanyOS\Domain\Webhook\Infrastructure\Service\WebhookDispatcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'domain.event.occurred')]
class WebhookEventListener
{
    public function __construct(
        private WebhookDispatcher $webhookDispatcher,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(DomainEventOccurred $event): void
    {
        $domainEvent = $event->getDomainEvent();
        
        $this->logger->info('Webhook event detected', [
            'event_name' => $domainEvent->getEventName(),
            'event_id' => $domainEvent->getEventId(),
            'aggregate_id' => $domainEvent->getAggregateId(),
            'occurred_at' => $domainEvent->getOccurredAt()->format('c')
        ]);

        // Webhook auslösen
        $this->webhookDispatcher->dispatch($domainEvent);
    }
} 