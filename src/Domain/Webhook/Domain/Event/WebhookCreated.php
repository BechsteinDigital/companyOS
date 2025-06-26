<?php

namespace CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Event;

use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Bundle\CoreBundle\Domain\Event\DomainEvent;

class WebhookCreated extends DomainEvent
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
        return 'webhook.created';
    }

    public function getEventVersion(): string
    {
        return '1';
    }
} 