<?php

declare(strict_types=1);

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\QueryHandler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetActivePluginsQuery;
use CompanyOS\Bundle\CoreBundle\Application\Plugin\DTO\PluginResponse;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepositoryInterface;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandlerInterface;

final class GetActivePluginsQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly PluginRepositoryInterface $pluginRepository
    ) {
    }

    public function __invoke(GetActivePluginsQuery $query): array
    {
        $plugins = $this->pluginRepository->findActive($query->getCategory());
        
        return array_map(
            fn($plugin) => new PluginResponse(
                id: $plugin->getId()->value(),
                name: $plugin->getName()->value(),
                displayName: $plugin->getDisplayName()->value(),
                description: $plugin->getDescription()->value(),
                version: $plugin->getVersion()->value(),
                author: $plugin->getAuthor()->value(),
                category: $plugin->getCategory()->value(),
                status: $plugin->getStatus()->value(),
                isActive: $plugin->isActive(),
                dependencies: $plugin->getDependencies(),
                settings: $plugin->getSettings(),
                createdAt: $plugin->getCreatedAt()->format('Y-m-d H:i:s'),
                updatedAt: $plugin->getUpdatedAt()->format('Y-m-d H:i:s')
            ),
            $plugins
        );
    }
} 