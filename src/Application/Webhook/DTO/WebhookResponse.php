<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\DTO;

use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity\Webhook;

class WebhookResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $url,
        public readonly array $events,
        public readonly bool $isActive,
        public readonly ?string $secret,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {
    }

    public static function fromEntity(Webhook $webhook): self
    {
        return new self(
            id: (string) $webhook->getId(),
            name: $webhook->getName(),
            url: $webhook->getUrl(),
            events: $webhook->getEvents(),
            isActive: $webhook->isActive(),
            secret: $webhook->getSecret(),
            createdAt: $webhook->getCreatedAt()->format('c'),
            updatedAt: $webhook->getUpdatedAt()->format('c')
        );
    }
} 