<?php

namespace CompanyOS\Domain\Webhook\Application\CommandHandler;

use CompanyOS\Domain\Webhook\Application\Command\UpdateWebhookCommand;
use CompanyOS\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;

class UpdateWebhookCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository
    ) {
    }

    public function __invoke(UpdateWebhookCommand $command): void
    {
        $webhook = $this->webhookRepository->findById($command->id);
        if (!$webhook) {
            throw new \InvalidArgumentException('Webhook not found');
        }

        $webhook->update(
            $command->name,
            $command->url,
            $command->events,
            $command->secret
        );

        $this->webhookRepository->save($webhook);
    }
} 