<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\DeletePluginCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginDeleted;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeletePluginCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PluginManager $pluginManager,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(DeletePluginCommand $command): void
    {
        if (empty($command->pluginId)) {
            throw new \InvalidArgumentException('Plugin ID is required');
        }

        $pluginId = Uuid::fromString($command->pluginId);
        
        // Get plugin before deletion for event data
        $plugin = $this->pluginManager->getPluginById($pluginId);
        if (!$plugin) {
            throw new \InvalidArgumentException('Plugin not found');
        }

        // Check if plugin is deactivated
        if ($plugin->isActive()) {
            throw new \InvalidArgumentException('Plugin must be deactivated before deletion');
        }

        // Delete plugin
        $this->pluginManager->deletePlugin($pluginId);

        // Dispatch domain event
        $event = new PluginDeleted(
            $pluginId,
            $plugin->getName(),
            $plugin->getVersion()
        );

        $this->eventDispatcher->dispatch($event);
    }
} 