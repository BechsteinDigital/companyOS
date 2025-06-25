<?php

namespace CompanyOS\Domain\Webhook\Domain\Event;

use CompanyOS\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Domain\Event\DomainEvent;

class WebhookDeleted extends DomainEvent
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
        return 'webhook.deleted';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 