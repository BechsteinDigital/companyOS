<?php

namespace CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\Handler;

use CompanyOS\Bundle\CoreBundle\Application\Plugin\Query\GetPluginQuery;
use CompanyOS\Bundle\CoreBundle\Domain\Plugin\Domain\Repository\PluginRepository;
use CompanyOS\Bundle\CoreBundle\Application\Query\QueryHandler;
use CompanyOS\Bundle\CoreBundle\Domain\ValueObject\Uuid;

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