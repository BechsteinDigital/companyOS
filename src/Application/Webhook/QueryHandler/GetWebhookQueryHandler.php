<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Webhook\Query\GetWebhookQuery;
use CompanyOS\Bundle\CoreBundle\Application\Webhook\DTO\WebhookResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetWebhookQueryHandler
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository
    ) {
    }

    public function __invoke(GetWebhookQuery $query): ?WebhookResponse
    {
        $webhook = $this->webhookRepository->findById(Uuid::fromString($query->id));
        
        if (!$webhook) {
            return null;
        }

        return new WebhookResponse(
            id: (string)$webhook->getId(),
            name: $webhook->getName(),
            url: $webhook->getUrl(),
            eventTypes: $webhook->getEventTypes(),
            isActive: $webhook->isActive(),
            secret: $webhook->getSecret(),
            createdAt: $webhook->getCreatedAt(),
            updatedAt: $webhook->getUpdatedAt()
        );
    }
} 