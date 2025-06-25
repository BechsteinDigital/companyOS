<?php

namespace CompanyOS\Application\Plugin\QueryHandler;

use CompanyOS\Application\Plugin\Query\GetAllPluginsQuery;
use CompanyOS\Application\Plugin\DTO\PluginResponse;
use CompanyOS\Domain\Plugin\Domain\Service\PluginManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAllPluginsQueryHandler
{
    public function __construct(
        private PluginManager $pluginManager
    ) {
    }

    public function __invoke(GetAllPluginsQuery $query): array
    {
        $plugins = $query->activeOnly 
            ? $this->pluginManager->getActivePluginEntities()
            : $this->pluginManager->getAllPluginEntities();

        return array_map(
            fn($plugin) => new PluginResponse(
                id: (string)$plugin->getId(),
                name: $plugin->getName(),
                version: $plugin->getVersion(),
                author: $plugin->getAuthor(),
                isActive: $plugin->isActive(),
                meta: $plugin->getMeta(),
                installedAt: $plugin->getCreatedAt(),
                updatedAt: $plugin->getUpdatedAt()
            ),
            $plugins
        );
    }
} 