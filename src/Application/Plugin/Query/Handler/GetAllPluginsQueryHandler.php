<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\Handler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetAllPluginsQuery;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandler;

class GetAllPluginsQueryHandler implements QueryHandler
{
    public function __construct(
        private PluginRepository $pluginRepository
    ) {
    }

    public function __invoke(GetAllPluginsQuery $query): array
    {
        $plugins = $query->activeOnly 
            ? $this->pluginRepository->findActive() 
            : $this->pluginRepository->findAll();

        return array_map(function ($plugin) {
            return [
                'id' => (string)$plugin->getId(),
                'name' => $plugin->getName(),
                'version' => $plugin->getVersion(),
                'author' => $plugin->getAuthor(),
                'active' => $plugin->isActive(),
                'meta' => $plugin->getMeta(),
                'createdAt' => $plugin->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $plugin->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $plugins);
    }
} 