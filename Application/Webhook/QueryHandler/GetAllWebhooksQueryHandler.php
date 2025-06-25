<?php

namespace CompanyOS\Domain\Webhook\Application\QueryHandler;

use CompanyOS\Domain\Webhook\Application\Query\GetAllWebhooksQuery;
use CompanyOS\Domain\Webhook\Application\DTO\WebhookResponse;
use CompanyOS\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAllWebhooksQueryHandler
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository
    ) {
    }

    public function __invoke(GetAllWebhooksQuery $query): array
    {
        $webhooks = $this->webhookRepository->findAll(
            activeOnly: $query->activeOnly,
            eventType: $query->eventType,
            search: $query->search,
            limit: $query->limit,
            offset: $query->offset
        );

        return array_map(
            fn($webhook) => new WebhookResponse(
                id: (string)$webhook->getId(),
                name: $webhook->getName(),
                url: $webhook->getUrl(),
                eventTypes: $webhook->getEventTypes(),
                isActive: $webhook->isActive(),
                secret: $webhook->getSecret(),
                createdAt: $webhook->getCreatedAt(),
                updatedAt: $webhook->getUpdatedAt()
            ),
            $webhooks
        );
    }
} 