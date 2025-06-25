<?php

namespace CompanyOS\Domain\Webhook\Domain\Event;

use CompanyOS\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Domain\Shared\Event\DomainEvent;

class WebhookUpdated extends DomainEvent
{
    public function __construct(
        private Webhook $webhook
    ) {
        parent::__construct();
    }

    public function getWebhook(): Webhook
    {
        return $this->webhook;
    }

    public function getEventName(): string
    {
        return 'webhook.updated';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 