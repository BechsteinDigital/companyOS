<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Webhook\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Webhook\Command\CreateWebhookCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Entity\Webhook;
use CompanyOS\Bundle\CoreBundle\Domain\Webhook\Domain\Repository\WebhookRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

class CreateWebhookCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private WebhookRepositoryInterface $webhookRepository
    ) {
    }

    public function __invoke(CreateWebhookCommand $command): Webhook
    {
        // Check if webhook with same name already exists
        $existingWebhook = $this->webhookRepository->findByName($command->name);
        if ($existingWebhook) {
            throw new \InvalidArgumentException('Webhook with this name already exists');
        }

        // Check if webhook with same URL already exists
        $existingWebhook = $this->webhookRepository->findByUrl($command->url);
        if ($existingWebhook) {
            throw new \InvalidArgumentException('Webhook with this URL already exists');
        }

        // Validate events
        $this->validateEvents($command->events);

        // Create webhook
        $webhook = new Webhook(
            Uuid::random(),
            $command->name,
            $command->url,
            $command->events,
            $command->secret
        );

        $this->webhookRepository->save($webhook);

        return $webhook;
    }

    private function validateEvents(array $events): void
    {
        $validEvents = [
            'plugin.installed',
            'plugin.activated',
            'plugin.deactivated',
            'plugin.deleted',
            'user.created',
            'user.updated',
            'user.deleted',
            'role.created',
            'role.updated',
            'role.deleted'
        ];

        foreach ($events as $event) {
            if (!in_array($event, $validEvents, true)) {
                throw new \InvalidArgumentException("Invalid event: {$event}");
            }
        }
    }
} 