<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\CommandHandler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Command\DeactivatePluginCommand;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Event\PluginDeactivated;
use CompanyOS\Bundle\CoreBundle\Application\Command\CommandHandlerInterface;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;
use CompanyOS\Bundle\CoreBundle\Infrastructure\Event\DomainEventDispatcher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeactivatePluginCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private PluginManager $pluginManager,
        private DomainEventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(DeactivatePluginCommand $command): void
    {
        if (empty($command->pluginId)) {
            throw new \InvalidArgumentException('Plugin ID is required');
        }

        $pluginId = Uuid::fromString($command->pluginId);
        $this->pluginManager->deactivatePlugin($pluginId);

        // Get plugin info for event
        $plugin = $this->pluginManager->getPluginById($pluginId);
        if ($plugin) {
            // Dispatch domain event
            $event = new PluginDeactivated(
                $pluginId,
                $plugin->getName(),
                $plugin->getVersion()
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
} 