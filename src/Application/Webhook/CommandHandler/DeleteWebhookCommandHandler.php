<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Webhook\Command\DeleteWebhookCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;

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