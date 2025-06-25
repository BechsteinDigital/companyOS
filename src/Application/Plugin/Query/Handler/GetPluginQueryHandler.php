<?php

namespace CompanyOS\Domain\Plugin\Application\Query\Handler;

use CompanyOS\Domain\Plugin\Application\Query\GetPluginQuery;
use CompanyOS\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Application\Query\QueryHandler;
use CompanyOS\Domain\ValueObject\Uuid;

class GetPluginQueryHandler implements QueryHandler
{
    public function __construct(
        private PluginRepository $pluginRepository
    ) {
    }

    public function __invoke(GetPluginQuery $query): ?array
    {
        $plugin = $this->pluginRepository->findById(Uuid::fromString($query->pluginId));
        
        if ($plugin === null) {
            return null;
        }

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
    }
} 