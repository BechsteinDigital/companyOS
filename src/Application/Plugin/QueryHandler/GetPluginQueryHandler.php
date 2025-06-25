<?php

namespace CompanyOS\Domain\Plugin\Application\QueryHandler;

use CompanyOS\Domain\Plugin\Application\Query\GetPluginQuery;
use CompanyOS\Domain\Plugin\Application\DTO\PluginResponse;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use CompanyOS\Domain\ValueObject\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPluginQueryHandler
{
    public function __construct(
        private PluginManager $pluginManager
    ) {
    }

    public function __invoke(GetPluginQuery $query): ?PluginResponse
    {
        $plugin = $this->pluginManager->getPluginEntity(Uuid::fromString($query->id));
        
        if (!$plugin) {
            return null;
        }

        return new PluginResponse(
            id: (string)$plugin->getId(),
            name: $plugin->getName(),
            version: $plugin->getVersion(),
            author: $plugin->getAuthor(),
            isActive: $plugin->isActive(),
            meta: $plugin->getMeta(),
            installedAt: $plugin->getCreatedAt(),
            updatedAt: $plugin->getUpdatedAt()
        );
    }
} 