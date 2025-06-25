<?php

namespace CompanyOS\Application\Webhook\CommandHandler;

use CompanyOS\Application\Webhook\Command\DeleteWebhookCommand;
use CompanyOS\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Application\Command\CommandHandlerInterface;

class DeleteWebhookCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository
    ) {
    }

    public function __invoke(DeleteWebhookCommand $command): void
    {
        $webhook = $this->webhookRepository->findById($command->id);
        if (!$webhook) {
            throw new \InvalidArgumentException('Webhook not found');
        }

        $this->webhookRepository->delete($webhook);
    }
} 